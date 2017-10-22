<?php

namespace app\conversations;

use Carbon\Carbon;
use app\models\Chat;
use app\handlers\ReminderHandler;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class RemindConversation extends Conversation
{
    protected $what;
    protected $when;
    protected $time;
    protected $interval;

    public function run()
    {
        return $this->next();
    }

    public function askWhat()
    {
        $this->ask('Input text of a reminder', function (Answer $answer) {
            $this->what = $answer->getText();
            $this->next();
        });
    }

    public function askWhen()
    {
        $question = Question::create('When to remind?')
            ->addButtons([
                Button::create('Today')->value('today'),
                Button::create('Tomorrow')->value('tomorrow'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->when = $answer->isInteractiveMessageReply() ?
                $answer->getValue() : $answer->getText();
            $this->next();
        });
    }

    public function askTime()
    {
        $this->ask('What time?', function (Answer $answer) {
            $this->time = $answer->getText();
            $this->next();
        });
    }

    public function askInterval()
    {
        $question = Question::create('What interval?')
            ->addButtons([
                Button::create('Once')->value('once'),
                Button::create('Daily')->value('daily'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->interval = $answer->isInteractiveMessageReply() ? $answer->getValue() : $answer->getText();
            $this->next();
        });
    }

    private function next()
    {
        $routes = [
            'what' => function () { return $this->askWhat(); },
            'when' => function () { return $this->askWhen(); },
            'time' => function () { return $this->askTime(); },
            'interval' => function () { return $this->askInterval(); },
        ];

        foreach ($routes as $prop => $fn) {
            if ($this->$prop === null) {
                return $fn();
            }
        }

        return $this->createReminder();
    }

    private function createReminder()
    {
        try {
            $chatId = $this->bot->getMessage()->getPayload()['chat']['id'];
            $chat = Chat::where('chatId', $chatId)->first();

            $when = Carbon::parse("{$this->when} {$this->time}", $chat->timezone);
        } catch (\Exception $e) {
            $this->say('Error in parsing time.');
            $this->when = $this->time = null;

            return $this->next();
        }

        $r = (new ReminderHandler())->create($this->bot, [
            'what' => $this->what,
            'when' => $when->toIso8601String(),
            'time' => $this->time,
            'interval' => $this->interval,
        ]);

        return $this->say("Reminder scheduled to {$r->when} 👍");
    }
}
