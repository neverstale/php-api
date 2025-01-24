# Neverstale PHP API wrapper

Wraps the [Neverstale API](http://docs.neverstale.io/api) for use in PHP applications.

## Classes

### [Client](./src/Client.php)

The main class for interacting with the Neverstale API. Uses Guzzle to make HTTP requests.

#### Methods

Mutating methods return a `TransactionResult` model. Non-mutating methods return a model representing the response 
from the API, or a simple boolean in the case of `health()`.

- `health(): bool` - Pings the Neverstale API to check if it is available and the API key is valid.  
- `ingest(array $data, array $callbackConfig = []): TransactionResult` - Ingests content to Neverstale.
- `batchDelete(array $ids): TransactionResult` - Deletes content from Neverstale by content ID or custom ID.
- `retrieve(string $id): Content` - Retrieves content from Neverstale by content ID or custom ID.
- `ignoreFlag(string $flagId): TransactionResult` - Ignores a flag in Neverstale.  
- `rescheduleFlag(string $flagId, DateTime $expiredAt): TransactionResult` - Reschedules a flag in Neverstale.  

### [Analysis Status](./src/enums/AnalysisStatus.php)

Represents the status of content analysis in Neverstale

#### Cases:

- `UNSENT`
- `STALE`
- `PENDING_INITIAL_ANALYSIS`
- `PENDING_REANALYSIS`
- `PENDING_TOKEN_AVAILABILITY`
- `PROCESSING_REANALYSIS`
- `PROCESSING_INITIAL_ANALYSIS`
- `ANALYZED_CLEAN`
- `ANALYZED_FLAGGED`
- `ANALYZED_ERROR`
- `UNKNOWN`
- `API_ERROR`

#### Methods:

- `label(): string` - Returns the human readable English language label for the status.

### Models

- `[TransactionResult](./src/models/TransactionResult.php)` Represents the result of a transaction with Neverstale.
- `[Content](./src/models/Content.php)` Represents content in Neverstale. [See the docs](https://docs.neverstale.io/api/content.html) for more details 
- `[Flag](./src/models/Flag.php)` Represents a flag in Neverstale. [See the docs](https://docs.neverstale.io/api/flags.html) for more details
