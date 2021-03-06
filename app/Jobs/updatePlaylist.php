<?php

namespace App\Jobs;

use App\Halo5\Definitions\Playlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Halo5\Models\Playlist as PlaylistModel;

class updatePlaylist extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Playlist
     */
    protected $playlist;

    /**
     * Some playlists are internal, but set as isRanked.
     * We can't rely on isActive, because some playlists in the past.
     * @var array
     */
    protected $ignoredPlaylists = [
        'eef52f20-860c-4ec2-84df-dda8947668cb', // 2p Progression
    ];

    /**
     * updatePlaylist constructor.
     * @param Playlist $playlist
     */
    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * Execute the job.we ha.
     *
     * @return void
     */
    public function handle()
    {
        try {
            /** @var $playlist PlaylistModel */
            $playlist = PlaylistModel::where('contentId', $this->playlist->contentId)->firstOrFail();
            $playlist->name = $this->playlist->name;
            $playlist->isRanked = $this->playlist->isRanked;
            $playlist->touch();
            $playlist->save();
        } catch (ModelNotFoundException $ex) {
            $playlist = new PlaylistModel();
            $playlist->contentId = $this->playlist->contentId;
            $playlist->name = $this->playlist->name;
            $playlist->description = $this->playlist->description;
            $playlist->isRanked = $this->playlist->isRanked;
            $playlist->save();
        }
    }
}
