<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum MessageType: string
{
    case Text = 'text';
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Location = 'location';
    case Contact = 'contact';
    case Sticker = 'sticker';
    case Poll = 'poll';
    case Reaction = 'reaction';

    public function label(): string
    {
        return match ($this) {
            self::Text => __('refineder-crm::messages.types.text'),
            self::Image => __('refineder-crm::messages.types.image'),
            self::Video => __('refineder-crm::messages.types.video'),
            self::Audio => __('refineder-crm::messages.types.audio'),
            self::Document => __('refineder-crm::messages.types.document'),
            self::Location => __('refineder-crm::messages.types.location'),
            self::Contact => __('refineder-crm::messages.types.contact'),
            self::Sticker => __('refineder-crm::messages.types.sticker'),
            self::Poll => __('refineder-crm::messages.types.poll'),
            self::Reaction => __('refineder-crm::messages.types.reaction'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'heroicon-o-chat-bubble-left',
            self::Image => 'heroicon-o-photo',
            self::Video => 'heroicon-o-video-camera',
            self::Audio => 'heroicon-o-microphone',
            self::Document => 'heroicon-o-document',
            self::Location => 'heroicon-o-map-pin',
            self::Contact => 'heroicon-o-user',
            self::Sticker => 'heroicon-o-face-smile',
            self::Poll => 'heroicon-o-chart-bar',
            self::Reaction => 'heroicon-o-heart',
        };
    }
}
