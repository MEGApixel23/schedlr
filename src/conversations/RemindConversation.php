<?php

namespace app\conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class RemindConversation extends Conversation
{
    protected $what;
    protected $when;
    protected $time;
    protected $interval;

    public function run()
    {
        return $this->askWhat();
    }

    public function askWhat()
    {
        $this->ask('What?', function (Answer $answer) {
            $this->what = $answer->getText();
            $this->askWhen();
        });
    }

    public function askWhen()
    {
        $question = Question::create('When ----------------------->?')
            ->fallback('Unable to create a new database')
            ->callbackId('create_database')
            ->addButtons([
                Button::create('Today')->value('today'),
                Button::create('Tomorrow')->value('tomorrow'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->when = $answer->getValue();
            } else {
                $this->when = $answer->getText();
            }

            $this->askTime();
        });
    }

    public function askTime()
    {
        $this->ask('What time?', function (Answer $answer) {
            $this->time = $answer->getText();
            $this->askInterval();
        });
    }

    public function askInterval()
    {
        $question = Question::create('What interval?')
            ->fallback('Unable to create a new database')
            ->callbackId('create_interval')
            ->addButtons([
                Button::create('Once')->value('once'),
                Button::create('Daily')->value('daily'),
                Button::create('Weekly')->value('weekly'),
                Button::create('Monthly')->value('monthly'),
                Button::create('Yearly')->value('yearly'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->interval = $answer->getValue();
            } else {
                $this->interval = $answer->getText();
            }

            $this->say(json_encode([
                '$what' => $this->what,
                '$when' => $this->when,
                '$time' => $this->time,
                '$interval' => $this->interval,
            ]));
        });
    }

    public function stopConversation(Message $message)
    {
        if ($message->getMessage() == 'stop') {
            return true;
        }

        return false;
    }
}
