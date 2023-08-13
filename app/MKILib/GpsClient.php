<?php

namespace App\MKILib;

use Exception;
use Illuminate\Support\Facades\Config;

use Google\Cloud\PubSub\PubSubClient;

class GpsClient {

    protected string $keyFile;
    protected string $projectId;

    public function __construct()
    {
        $this->keyFile = env('GOOGLE_PUBSUB_KEY');
        $this->projectId = env('GOOGLE_PUBSUB_PROJECT_ID');
    }

    public function publisher() {

        try {
            $pubSub = new PubSubClient([
                'keyFilePath' => base_path(). '/'. $this->keyFile,
                'projectId' => $this->projectId
            ]);

            $topic = $pubSub->topic('projects/eproc-holding-dev/topics/notification');

            $response = $topic->publish([
                'data' => 'Pesan baru Output 1',
                'attributes' => [
                    'location' => 'Detroit'
                ]
            ]);
            return $response['messageIds'];
        } catch(Exception $e) {
            return $e;
        }
    }

    public function subscriber() {
        try {
            $pubSub = new PubSubClient([
                'keyFilePath' => base_path(). '/'. $this->keyFile,
                'projectId' => $this->projectId
            ]);

            $subscription = $pubSub->subscription('projects/eproc-holding-dev/subscriptions/notification-sub');
            $messages = $subscription->pull();

            $response = array();
            foreach ($messages as $message) {
                $response[] = array(
                    'data' => $message->data(),
                    'attributes' => $message->attributes()
                );
            }

            return $response;
        } catch(Exception $e) {
            return null;
        }

    }
}
