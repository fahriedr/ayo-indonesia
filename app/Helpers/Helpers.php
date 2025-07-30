<?php

namespace App\Helpers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait Helpers {

    public function uploadFile($file, $path = 'team') {

        $file_decode = $this->revalidateBase64File($file);

        $full_path = $path . '/' . $file_decode['name'];

        $res = Storage::disk("public")->put($full_path, $file_decode['file']);

        return $full_path;
    }

    public function revalidateBase64File($file)
    {
        $image_64 = $file;
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

        // find substring fro replace here eg: data:image/png;base64,

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = md5(microtime()).'.'.$extension;

        return [
            'file' => base64_decode($image),
            'name' => $imageName,
        ];
    }

    public function validateGoal(Request $request, Game $game, Team $team, Player $player)
    {

        $exists = $team->home_games()->where('id', $game->id)->exists()
        || $team->away_games()->where('id', $game->id)->exists();

        // Check if the player belongs to the team
        if(!$exists) {
            throw new Exception('The game does not belong to the specified team', 400);
        }

        // Check if the player belongs to the game
        if ($player->team_id != $game->home_team_id && $player->team_id != $game->away_team_id) {
            throw new Exception('The player does not belong to the team of the game', 400);
        } 

        // Check if the assist player belongs to the game
        if ($request->assist_player_id) {
            $assist_player = Player::find($request->assist_player_id);

            if ($assist_player->team_id != $game->home_team_id && $assist_player->team_id != $game->away_team_id) {
                throw new Exception('The assist player does not belong to the team of the game', 400);
            }
        }

        // Validate the goal conditions
        if (!$request->is_own_goal && $team->id != $player->team_id) {
            throw new Exception('The player ID does not match the authenticated player', 400);
        }

        // Check if the goal is a penalty or own goal and validate accordingly
        if ($request->is_own_goal && $request->is_penalty) {
            throw new Exception('Own goal cannot be penalty', 400);
        }

        // Check if the goal is an own goal or penalty and validate assist player
        if ($request->is_own_goal && $request->assist_player_id) {
            throw new Exception('Own goal cannot have an assist player', 400);
        }

        // Check if the goal is a penalty and validate assist player
        if ($request->is_penalty && $request->assist_player_id) {
            throw new Exception('Penalty goal cannot have an assist player', 400);
        }

        // Check if the goal is an own goal and validate team ID
        if ($request->is_own_goal && $request->team_id == $player->team_id) {
            throw new Exception('Own goal must be scored by the other team', 400);
        }

        // Check if the goal is a penalty and validate team ID
        if ($request->is_penalty && $request->team_id != $player->team_id) {
            throw new Exception('Penalty goal must be scored by the team of the player', 400);
        }

        return true;

    }
}