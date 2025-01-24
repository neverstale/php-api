<?php

namespace neverstale\api\models;

class Flag extends BaseModel
{
    public string $id;
    public string $flag;
    public string $reason;
    public string $snippet;
    public \DateTime $last_analyzed_at;
    public \DateTime $expired_at;
    public ?\DateTime $ignored_at;
}
