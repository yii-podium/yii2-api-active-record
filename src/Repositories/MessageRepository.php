<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\MessageActiveRecord;
use Podium\ActiveRecordApi\Enums\MessageSide;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;

use function is_int;

final class MessageRepository implements MessageRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = MessageActiveRecord::class;

    private ?MessageActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): MessageActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(MessageActiveRecord $model): void
    {
        $this->model = $model;
    }

    public function getParent(): MessageRepositoryInterface
    {
        $message = $this->getModel()->replyTo;

        $parent = new self();
        $parent->setModel($message);

        return $parent;
    }

    public function getId(): int
    {
        return $this->getModel()->id;
    }

    public function getParticipant(MemberRepositoryInterface $member): MessageParticipantRepositoryInterface
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }

        $sender = $this->getModel()->sender;
        $receiver = $this->getModel()->receiver;

        $participant = new MessageParticipantRepository();

        if ($sender && $sender->member_id === $memberId) {
            $participant->setModel($sender);
        } elseif ($receiver && $receiver->member_id === $memberId) {
            $participant->setModel($receiver);
        }

        return $participant;
    }

    public function isCompletelyDeleted(): bool
    {
        $sender = $this->getModel()->sender;
        $receiver = $this->getModel()->receiver;

        return null === $sender && null === $receiver;
    }

    public function send(
        MemberRepositoryInterface $sender,
        MemberRepositoryInterface $receiver,
        MessageRepositoryInterface $replyTo = null,
        array $data = []
    ): bool {
        /** @var MessageActiveRecord $message */
        $message = new $this->activeRecordClass();

        if (!$message->load($data, '')) {
            return false;
        }

        if ($replyTo) {
            $replyToId = $replyTo->getId();
            if (!is_int($replyToId)) {
                throw new DomainException('Invalid reply ID!');
            }

            if (!$replyTo->isProperReply($sender, $receiver)) {
                return false;
            }

            $message->reply_to_id = $replyToId;
        }

        if (!$message->save()) {
            $this->errors = $message->errors;

            return false;
        }

        $this->setModel($message);

        $messageSender = new MessageParticipantRepository();
        if (!$messageSender->copy($this, $sender, MessageSide::SENDER)) {
            $this->errors = $messageSender->getErrors();

            return false;
        }

        $messageReceiver = new MessageParticipantRepository();
        if (!$messageReceiver->copy($this, $receiver, MessageSide::RECEIVER)) {
            $this->errors = $messageReceiver->getErrors();

            return false;
        }

        return true;
    }

    public function isProperReply(
        MemberRepositoryInterface $replySender,
        MemberRepositoryInterface $replyReceiver
    ): bool {
        $originalSender = $this->getModel()->sender;
        $originalReceiver = $this->getModel()->receiver;

        return $originalSender
            && $originalSender->member_id === $replyReceiver->getId()
            && $originalReceiver
            && $originalReceiver->member_id === $replySender->getId();
    }

    public function verifyParticipants(MemberRepositoryInterface $sender, MemberRepositoryInterface $receiver): bool
    {
        // TODO: Implement verifyParticipants() method.
    }

    public function getAuthor(): MemberRepositoryInterface
    {
        // TODO: Implement getAuthor() method.
    }

    public function getAllowedGroups(): array
    {
        // TODO: Implement getAllowedGroups() method.
    }
}
