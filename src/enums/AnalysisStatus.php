<?php

namespace neverstale\api\enums;

/**
 * Neverstale Analysis Status Enum
 *
 * Represents the status of content analysis in Neverstale
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
enum AnalysisStatus: string
{
    case UNSENT = 'unsent';
    case STALE = 'stale';
    case PENDING_INITIAL_ANALYSIS = 'pending-initial-analysis';
    case PENDING_REANALYSIS = 'pending-reanalysis';
    case PENDING_TOKEN_AVAILABILITY = 'pending-token-availability';
    case PROCESSING_REANALYSIS = 'processing-reanalysis';
    case PROCESSING_INITIAL_ANALYSIS = 'processing-initial-analysis';
    case ANALYZED_CLEAN = 'analyzed-clean';
    case ANALYZED_FLAGGED = 'analyzed-flagged';
    case ANALYZED_ERROR = 'analyzed-error';
    case UNKNOWN = 'unknown';
    case API_ERROR = 'api-error';

    public function label(): string
    {
        return match ($this) {
            self::UNSENT => 'Unsent',
            self::STALE => 'Stale',
            self::PENDING_INITIAL_ANALYSIS => 'Pending Initial Analysis',
            self::PENDING_REANALYSIS => 'Pending Reanalysis',
            self::PROCESSING_REANALYSIS, self::PROCESSING_INITIAL_ANALYSIS => 'Processing',
            self::ANALYZED_CLEAN => 'Clean',
            self::ANALYZED_FLAGGED => 'Flagged',
            self::ANALYZED_ERROR => 'Error',
            self::API_ERROR => 'API Error',
            default => 'Unknown',
        };
    }
}
