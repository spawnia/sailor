<?php declare(strict_types=1);

use Spawnia\Sailor\InlineFragments\Operations\SearchQuery;

require __DIR__ . '/vendor/autoload.php';

$result = SearchQuery::execute('test query')->errorFree();

$article = $result->data->search[0];
assert($article instanceof SearchQuery\Search\Article);
assert($article->id === '1');
assert($article->title === 'Test Article');
assert($article->content->text === 'Article text');

$video = $result->data->search[1];
assert($video instanceof SearchQuery\Search\Video);
assert($video->id === '2');
assert($video->title === 'Test Video');
assert($video->content->url === 'https://example.com/video.mp4');
assert($video->content->duration === 120);
