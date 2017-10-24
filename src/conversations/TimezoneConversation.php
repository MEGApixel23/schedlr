<?php

namespace app\conversations;

use function app\helpers\message;

use app\models\Chat;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\ButtonsRow;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class TimezoneConversation extends Conversation
{
    protected $timezone;

    public function run(): void
    {
        $question = Question::create(message('select_timezone'))
            ->addButtons($this->createButtons(4));
        $inst = $this;

        $this->bot->ask(
            $question,
            function (Answer $answer) use ($inst): void {
                $this->timezone = (int) ($answer->isInteractiveMessageReply() ?
                     $answer->getValue() : $answer->getText());
                $text = $inst->getTimezoneText($this->timezone);
                $chatId = $answer->getMessage()->getPayload()['chat']['id'];

                Chat::where('chatId', $chatId)->update(['timezone' => $this->timezone]);

                $this->say(
                    str_replace('{text}', $text, message('timezone_set'))
                );
            }
        );
    }

    private function createButtons(int $itemsInRow): array
    {
        $rows = [];
        $zones = range(-12, 11);

        for ($i = 0; $i < count($zones) - 1; $i++) {
            if ($i === 0 || $i % ($itemsInRow) === 0) {
                $rows[] = [];
            }

            $rows[count($rows) - 1][] = $zones[$i];
        }

        return array_map(function ($zones): ButtonsRow {
            return ButtonsRow::create(
                array_map(function ($zone): Button {
                    return Button::create($this->getTimezoneText($zone))
                        ->value($zone);
                }, $zones)
            );
        }, $rows);
    }

    public function getTimezoneText($zone): string
    {
        return $zone >= 0 ? "+{$zone} UTC" : "{$zone} UTC";
    }
}
