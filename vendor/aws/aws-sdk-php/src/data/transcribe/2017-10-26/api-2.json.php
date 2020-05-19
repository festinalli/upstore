<?php
// This file was auto-generated from sdk-root/src/data/transcribe/2017-10-26/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2017-10-26', 'endpointPrefix' => 'transcribe', 'jsonVersion' => '1.1', 'protocol' => 'json', 'serviceFullName' => 'Amazon Transcribe Service', 'serviceId' => 'Transcribe', 'signatureVersion' => 'v4', 'signingName' => 'transcribe', 'targetPrefix' => 'Transcribe', 'uid' => 'transcribe-2017-10-26', ], 'operations' => [ 'CreateVocabulary' => [ 'name' => 'CreateVocabulary', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateVocabularyRequest', ], 'output' => [ 'shape' => 'CreateVocabularyResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], [ 'shape' => 'ConflictException', ], ], ], 'DeleteTranscriptionJob' => [ 'name' => 'DeleteTranscriptionJob', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteTranscriptionJobRequest', ], 'errors' => [ [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'BadRequestException', ], [ 'shape' => 'InternalFailureException', ], ], ], 'DeleteVocabulary' => [ 'name' => 'DeleteVocabulary', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteVocabularyRequest', ], 'errors' => [ [ 'shape' => 'NotFoundException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'BadRequestException', ], [ 'shape' => 'InternalFailureException', ], ], ], 'GetTranscriptionJob' => [ 'name' => 'GetTranscriptionJob', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetTranscriptionJobRequest', ], 'output' => [ 'shape' => 'GetTranscriptionJobResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], [ 'shape' => 'NotFoundException', ], ], ], 'GetVocabulary' => [ 'name' => 'GetVocabulary', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetVocabularyRequest', ], 'output' => [ 'shape' => 'GetVocabularyResponse', ], 'errors' => [ [ 'shape' => 'NotFoundException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], [ 'shape' => 'BadRequestException', ], ], ], 'ListTranscriptionJobs' => [ 'name' => 'ListTranscriptionJobs', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTranscriptionJobsRequest', ], 'output' => [ 'shape' => 'ListTranscriptionJobsResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], ], ], 'ListVocabularies' => [ 'name' => 'ListVocabularies', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListVocabulariesRequest', ], 'output' => [ 'shape' => 'ListVocabulariesResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], ], ], 'StartTranscriptionJob' => [ 'name' => 'StartTranscriptionJob', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'StartTranscriptionJobRequest', ], 'output' => [ 'shape' => 'StartTranscriptionJobResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], [ 'shape' => 'ConflictException', ], ], ], 'UpdateVocabulary' => [ 'name' => 'UpdateVocabulary', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateVocabularyRequest', ], 'output' => [ 'shape' => 'UpdateVocabularyResponse', ], 'errors' => [ [ 'shape' => 'BadRequestException', ], [ 'shape' => 'LimitExceededException', ], [ 'shape' => 'InternalFailureException', ], [ 'shape' => 'NotFoundException', ], [ 'shape' => 'ConflictException', ], ], ], ], 'shapes' => [ 'BadRequestException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'FailureReason', ], ], 'exception' => true, ], 'Boolean' => [ 'type' => 'boolean', ], 'ConflictException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'CreateVocabularyRequest' => [ 'type' => 'structure', 'required' => [ 'VocabularyName', 'LanguageCode', 'Phrases', ], 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'Phrases' => [ 'shape' => 'Phrases', ], ], ], 'CreateVocabularyResponse' => [ 'type' => 'structure', 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'VocabularyState' => [ 'shape' => 'VocabularyState', ], 'LastModifiedTime' => [ 'shape' => 'DateTime', ], 'FailureReason' => [ 'shape' => 'FailureReason', ], ], ], 'DateTime' => [ 'type' => 'timestamp', ], 'DeleteTranscriptionJobRequest' => [ 'type' => 'structure', 'required' => [ 'TranscriptionJobName', ], 'members' => [ 'TranscriptionJobName' => [ 'shape' => 'TranscriptionJobName', ], ], ], 'DeleteVocabularyRequest' => [ 'type' => 'structure', 'required' => [ 'VocabularyName', ], 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], ], ], 'FailureReason' => [ 'type' => 'string', ], 'GetTranscriptionJobRequest' => [ 'type' => 'structure', 'required' => [ 'TranscriptionJobName', ], 'members' => [ 'TranscriptionJobName' => [ 'shape' => 'TranscriptionJobName', ], ], ], 'GetTranscriptionJobResponse' => [ 'type' => 'structure', 'members' => [ 'TranscriptionJob' => [ 'shape' => 'TranscriptionJob', ], ], ], 'GetVocabularyRequest' => [ 'type' => 'structure', 'required' => [ 'VocabularyName', ], 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], ], ], 'GetVocabularyResponse' => [ 'type' => 'structure', 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'VocabularyState' => [ 'shape' => 'VocabularyState', ], 'LastModifiedTime' => [ 'shape' => 'DateTime', ], 'FailureReason' => [ 'shape' => 'FailureReason', ], 'DownloadUri' => [ 'shape' => 'Uri', ], ], ], 'InternalFailureException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'String', ], ], 'exception' => true, 'fault' => true, ], 'LanguageCode' => [ 'type' => 'string', 'enum' => [ 'en-US', 'es-US', 'en-AU', 'fr-CA', 'en-UK', ], ], 'LimitExceededException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'ListTranscriptionJobsRequest' => [ 'type' => 'structure', 'members' => [ 'Status' => [ 'shape' => 'TranscriptionJobStatus', ], 'JobNameContains' => [ 'shape' => 'TranscriptionJobName', ], 'NextToken' => [ 'shape' => 'NextToken', ], 'MaxResults' => [ 'shape' => 'MaxResults', ], ], ], 'ListTranscriptionJobsResponse' => [ 'type' => 'structure', 'members' => [ 'Status' => [ 'shape' => 'TranscriptionJobStatus', ], 'NextToken' => [ 'shape' => 'NextToken', ], 'TranscriptionJobSummaries' => [ 'shape' => 'TranscriptionJobSummaries', ], ], ], 'ListVocabulariesRequest' => [ 'type' => 'structure', 'members' => [ 'NextToken' => [ 'shape' => 'NextToken', ], 'MaxResults' => [ 'shape' => 'MaxResults', ], 'StateEquals' => [ 'shape' => 'VocabularyState', ], 'NameContains' => [ 'shape' => 'VocabularyName', ], ], ], 'ListVocabulariesResponse' => [ 'type' => 'structure', 'members' => [ 'Status' => [ 'shape' => 'TranscriptionJobStatus', ], 'NextToken' => [ 'shape' => 'NextToken', ], 'Vocabularies' => [ 'shape' => 'Vocabularies', ], ], ], 'MaxResults' => [ 'type' => 'integer', 'max' => 100, 'min' => 1, ], 'MaxSpeakers' => [ 'type' => 'integer', 'max' => 10, 'min' => 2, ], 'Media' => [ 'type' => 'structure', 'members' => [ 'MediaFileUri' => [ 'shape' => 'Uri', ], ], ], 'MediaFormat' => [ 'type' => 'string', 'enum' => [ 'mp3', 'mp4', 'wav', 'flac', ], ], 'MediaSampleRateHertz' => [ 'type' => 'integer', 'max' => 48000, 'min' => 8000, ], 'NextToken' => [ 'type' => 'string', 'max' => 8192, ], 'NotFoundException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'OutputBucketName' => [ 'type' => 'string', 'pattern' => '[a-z0-9][\\.\\-a-z0-9]{1,61}[a-z0-9]', ], 'OutputLocationType' => [ 'type' => 'string', 'enum' => [ 'CUSTOMER_BUCKET', 'SERVICE_BUCKET', ], ], 'Phrase' => [ 'type' => 'string', 'max' => 256, 'min' => 0, ], 'Phrases' => [ 'type' => 'list', 'member' => [ 'shape' => 'Phrase', ], ], 'Settings' => [ 'type' => 'structure', 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'ShowSpeakerLabels' => [ 'shape' => 'Boolean', ], 'MaxSpeakerLabels' => [ 'shape' => 'MaxSpeakers', ], 'ChannelIdentification' => [ 'shape' => 'Boolean', ], ], ], 'StartTranscriptionJobRequest' => [ 'type' => 'structure', 'required' => [ 'TranscriptionJobName', 'LanguageCode', 'MediaFormat', 'Media', ], 'members' => [ 'TranscriptionJobName' => [ 'shape' => 'TranscriptionJobName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'MediaSampleRateHertz' => [ 'shape' => 'MediaSampleRateHertz', ], 'MediaFormat' => [ 'shape' => 'MediaFormat', ], 'Media' => [ 'shape' => 'Media', ], 'OutputBucketName' => [ 'shape' => 'OutputBucketName', ], 'Settings' => [ 'shape' => 'Settings', ], ], ], 'StartTranscriptionJobResponse' => [ 'type' => 'structure', 'members' => [ 'TranscriptionJob' => [ 'shape' => 'TranscriptionJob', ], ], ], 'String' => [ 'type' => 'string', ], 'Transcript' => [ 'type' => 'structure', 'members' => [ 'TranscriptFileUri' => [ 'shape' => 'Uri', ], ], ], 'TranscriptionJob' => [ 'type' => 'structure', 'members' => [ 'TranscriptionJobName' => [ 'shape' => 'TranscriptionJobName', ], 'TranscriptionJobStatus' => [ 'shape' => 'TranscriptionJobStatus', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'MediaSampleRateHertz' => [ 'shape' => 'MediaSampleRateHertz', ], 'MediaFormat' => [ 'shape' => 'MediaFormat', ], 'Media' => [ 'shape' => 'Media', ], 'Transcript' => [ 'shape' => 'Transcript', ], 'CreationTime' => [ 'shape' => 'DateTime', ], 'CompletionTime' => [ 'shape' => 'DateTime', ], 'FailureReason' => [ 'shape' => 'FailureReason', ], 'Settings' => [ 'shape' => 'Settings', ], ], ], 'TranscriptionJobName' => [ 'type' => 'string', 'max' => 200, 'min' => 1, 'pattern' => '^[0-9a-zA-Z._-]+', ], 'TranscriptionJobStatus' => [ 'type' => 'string', 'enum' => [ 'IN_PROGRESS', 'FAILED', 'COMPLETED', ], ], 'TranscriptionJobSummaries' => [ 'type' => 'list', 'member' => [ 'shape' => 'TranscriptionJobSummary', ], ], 'TranscriptionJobSummary' => [ 'type' => 'structure', 'members' => [ 'TranscriptionJobName' => [ 'shape' => 'TranscriptionJobName', ], 'CreationTime' => [ 'shape' => 'DateTime', ], 'CompletionTime' => [ 'shape' => 'DateTime', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'TranscriptionJobStatus' => [ 'shape' => 'TranscriptionJobStatus', ], 'FailureReason' => [ 'shape' => 'FailureReason', ], 'OutputLocationType' => [ 'shape' => 'OutputLocationType', ], ], ], 'UpdateVocabularyRequest' => [ 'type' => 'structure', 'required' => [ 'VocabularyName', 'LanguageCode', 'Phrases', ], 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'Phrases' => [ 'shape' => 'Phrases', ], ], ], 'UpdateVocabularyResponse' => [ 'type' => 'structure', 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'LastModifiedTime' => [ 'shape' => 'DateTime', ], 'VocabularyState' => [ 'shape' => 'VocabularyState', ], ], ], 'Uri' => [ 'type' => 'string', 'max' => 2000, 'min' => 1, ], 'Vocabularies' => [ 'type' => 'list', 'member' => [ 'shape' => 'VocabularyInfo', ], ], 'VocabularyInfo' => [ 'type' => 'structure', 'members' => [ 'VocabularyName' => [ 'shape' => 'VocabularyName', ], 'LanguageCode' => [ 'shape' => 'LanguageCode', ], 'LastModifiedTime' => [ 'shape' => 'DateTime', ], 'VocabularyState' => [ 'shape' => 'VocabularyState', ], ], ], 'VocabularyName' => [ 'type' => 'string', 'max' => 200, 'min' => 1, 'pattern' => '^[0-9a-zA-Z._-]+', ], 'VocabularyState' => [ 'type' => 'string', 'enum' => [ 'PENDING', 'READY', 'FAILED', ], ], ],];
