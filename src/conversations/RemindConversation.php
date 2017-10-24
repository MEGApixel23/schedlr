<?php

namespace app\conversations;

use function app\helpers\message;

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
        $this->ask(message('input_text_of_reminder'), function (Answer $answer) {
            $this->what = $answer->getText();
            $this->next();
        });
    }

    public function askWhen()
    {
        $question = Question::create(message('when_to_remind_question'))
            ->addButtons([
                Button::create(message('when.today'))->value('today'),
                Button::create(message('when.tomorrow'))->value('tomorrow'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->when = $answer->isInteractiveMessageReply() ?
                $answer->getValue() : $answer->getText();
            $this->next();
        });
    }

    public function askTime()
    {
        $this->ask(message('what_time_question'), function (Answer $answer) {
            $this->time = $answer->getText();
            $this->next();
        });
    }

    public function askInterval()
    {
        $question = Question::create(message('what_interval_question'))
            ->addButtons([
                Button::create(message('interval.once'))->value('once'),
                Button::create(message('interval.daily'))->value('daily'),
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
            $this->say(message('error_in_parsing_time'));
            $this->when = $this->time = null;

            return $this->next();
        }

        $r = (new ReminderHandler())->create($this->bot, [
            'what' => $this->what,
            'when' => $when->toIso8601String(),
            'time' => $this->time,
            'interval' => $this->interval,
        ]);

        return $this->say(
            str_replace('{when}', $r->when, message('reminder_set'))
        );
    }
}
