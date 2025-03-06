<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Validator;

class TweetsController extends Controller
{
    public function twitConnection()
    {
        $consumer_key = env('TWITTER_CONSUMER_KEY');
        $consumer_secret = env('TWITTER_CONSUMER_SECRET');
        $access_token = env('TWITTER_ACCESS_TOKEN');
        $access_secret = env('TWITTER_ACCESS_SECRET');
        return new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_secret);
    }

    public function postTweet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:280',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $client = $this->twitConnection();

        $message = $request->message;
        $response = $client->post("tweets", ["text" => $message]);

        $httpCode = $client->getLastHttpCode();

        if ($httpCode >= 200 && $httpCode < 300) {
            return response()->json([
                "success" => true,
                "message" => "Tweet posted successfully!",
                "tweet" => $response
            ]);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Failed to post tweet",
                "details" => $response
            ], $httpCode);
        }
    }

    public function deleteTweetById($id)
    {
        $client = $this->twitConnection();

        $response = $client->delete("tweets/{$id}");

        $httpCode = $client->getLastHttpCode();

        if ($httpCode == 200) {
            return response()->json([
                "success" => true,
                "message" => "Tweet deleted successfully!"
            ], $httpCode);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Failed to delete tweet",
                "details" => $response
            ], $httpCode);
        }
    }

    public function updateTweetById(Request $request)
    {
        $this->postTweet($request->message);

        $id = $request->id;
        $message = $request->message;

        $client = $this->twitConnection();

        // delete
        $response = $client->delete("tweets/{$id}");
        $httpCode = $client->getLastHttpCode();

        if ($httpCode != 200) {
            return response()->json([
                "success" => false,
                "error" => "Failed to delete tweet",
                "details" => $response
            ], $httpCode);
        }
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:280',
        ]);

        // repost
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $message = $request->message;
        $response = $client->post("tweets", ["text" => $message]);

        $httpCode = $client->getLastHttpCode();

        if ($httpCode >= 200 && $httpCode < 300) {
            return response()->json([
                "success" => true,
                "message" => "Tweet posted successfully!",
                "tweet" => $response
            ]);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Failed to post tweet",
                "details" => $response
            ], $httpCode);
        }
    }

    public function getTweetById($id)
    {
        $client = $this->twitConnection();

        $response = $client->get("tweets/{$id}");

        $httpCode = $client->getLastHttpCode();

        if ($httpCode == 200) {
            return response()->json([
                "success" => true,
                "tweet" => $response
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Failed to fetch tweet",
                "details" => $response
            ], $httpCode);
        }
    }

    public function getMyTweets()
    {
        $client = $this->twitConnection();

        $user = $client->get("users/me");

        if (!isset($user->data->id)) {
            return response()->json([
                "success" => false,
                "error" => "Failed to fetch user details",
                "details" => $user
            ], 400);
        }

        $userId = $user->data->id;

        $response = $client->get("users/{$userId}/tweets");

        $httpCode = $client->getLastHttpCode();

        if ($httpCode == 200) {
            return response()->json([
                "success" => true,
                "tweets" => $response
            ]);
        } else {
            return response()->json([
                "success" => false,
                "error" => "Failed to fetch tweets",
                "details" => $response
            ], $httpCode);
        }
    }

    public function getTweetInteractions($id)
    {
        $client = $this->twitConnection();

        // Get users who liked the tweet
        $likes = $client->get("tweets/{$id}/liking_users");

        // Get users who retweeted the tweet
        $retweets = $client->get("tweets/{$id}/retweeted_by");

        // Get replies to the tweet
        $replies = $client->get("tweets/search/recent", [
            'query' => "conversation_id:{$id}"
        ]);

        return response()->json([
            "success" => true,
            "likes" => $likes,
            "retweets" => $retweets,
            "replies" => $replies
        ]);
    }
}
