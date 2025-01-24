<?php

namespace neverstale\api\models;

use neverstale\api\enums\AnalysisStatus;

/**
 * Neverstale Content Model
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
class Content extends BaseModel
{
    // Content ulid assigned by neverstale
    public string $id;
    // URL to the content in neverstale
    public string $permalink;
    // Custom id provided by you
    public string $custom_id;
    // Title of the content
    public string $title;
    // Author of the content
    public ?string $author;
    // URL of the content
    public ?string $url;
    // ulid of the channel assigned by neverstale
    public string $channel_id;
    // see possible analysis statuses belo
    public AnalysisStatus $analysis_status;
    /**
     * @var array<Flag>
     */
    public ?array $flags = [];

    public ?\DateTime $analyzed_at;
    public ?\DateTime $expired_at;
}
