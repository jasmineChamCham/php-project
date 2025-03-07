<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;

class TweetService
{
    private $client;

    public function __construct()
    {
        $consumer_key = env('TWITTER_CONSUMER_KEY');
        $consumer_secret = env('TWITTER_CONSUMER_SECRET');
        $access_token = env('TWITTER_ACCESS_TOKEN');
        $access_secret = env('TWITTER_ACCESS_SECRET');

        $this->client = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_secret);
    }

    public function store(string $message)
    {
        $response = $this->client->post("tweets", ["text" => $message]);
        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function destroy($id)
    {
        $response = $this->client->delete("tweets/{$id}");
        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function update($id, string $message)
    {
        $deleteResult = $this->destroy($id);

        if ($deleteResult['httpCode'] != 200) {
            return $deleteResult;
        }

        return $this->store($message);
    }

    public function show($id)
    {
        $response = $this->client->get("tweets/{$id}");
        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function myTweets()
    {
        $user = $this->client->get("users/me");

        if (!isset($user->data->id)) {
            return [
                'httpCode' => 400,
                'response' => "Failed to fetch user details: " . json_encode($user)
            ];
        }

        $userId = $user->data->id;
        $response = $this->client->get("users/{$userId}/tweets");

        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => $response
        ];
    }

    public function tweetInteractions($tweetId)
    {
        $likes = $this->client->get("tweets/{$tweetId}/liking_users");
        $retweets = $this->client->get("tweets/{$tweetId}/retweeted_by");
        $replies = $this->client->get("tweets/search/recent", [
            'query' => "conversation_id:{$tweetId}"
        ]);

        return [
            'httpCode' => $this->client->getLastHttpCode(),
            'response' => [
                "likes" => $likes,
                "retweets" => $retweets,
                "replies" => $replies
            ]
        ];
    }
}
