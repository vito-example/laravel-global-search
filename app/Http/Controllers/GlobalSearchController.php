<?php
/**
 *  app/Http/Controllers/GlobalSearchController.php
 *
 * Date-Time: 02.06.21
 * Time: 14:28
 * @author Vito Makhatadze <vitomaxatadze@gmail.com>
 */

namespace App\Http\Controllers;

use App\Http\Resources\GlobalSearchResource;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class GlobalSearchController
 * @package App\Http\Controllers
 */
class GlobalSearchController extends Controller
{
    private const BUFFER = 10;

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // request parameter
        $keyword = $request->search;
        $toExclude = [

        ];

        // Load all the model in the Models Directory
        $files = File::allFiles(app()->basePath(). '/app/Models');

        $results = collect($files)->map(function (SplFileInfo $fileInfo) {
            $fileName = $fileInfo->getRelativePathname();

            if (substr($fileName, -4) !== '.php') {
                return null;
            }

            return substr($fileName, 0, -4);
        })->filter(function (?string $className) use ($toExclude) {
            // filter out the ones that use the Searchable trait
            if ($className === null) {
                return false;
            }

            $reflection = new \ReflectionClass($this->modelNamespacePrefix() . $className);

            // check if class extends eloquent model

            $isModel = $reflection->isSubclassOf(Model::class);

            // check if class has search method
            $searchAble = $reflection->hasMethod('search');

            return $isModel && $searchAble && !in_array($reflection->getName(), $toExclude, true);
        })->map(function ($className) use ($keyword) {
            $model = app($this->modelNamespacePrefix() . $className);
            // for each model, we will call the search() scout function

            // against the search keyword supplied in the http request query.
            $fields = array_filter($model::SEARCHABLE_FIELDS, function ($field) {
                return $field !== 'id';
            });

            return $model::search($keyword)->get()->map(function ($modelRecord) use ($fields, $keyword, $className) {
                // for each search result, we want to include.

                // 1. match -- the matching text and its surrounding text

                $fieldsData = $modelRecord->only($fields);

                $serializedValues = collect($fieldsData)->join(' ');

                $searchPosition = stripos(strtolower($serializedValues), strtolower($keyword));
                if ($searchPosition !== false) {
                    $start = $searchPosition - self::BUFFER;

                    $start = $start < 0 ? 0 : $start;

                    $length = strlen($keyword) + 2 * self::BUFFER;

                    $sliced = substr($serializedValues, $start, $length);

                    $shouldAddPreFix = $start > 0;
                    $shouldAddPostFix = ($start + $length) < strlen($serializedValues);

                    $sliced = $shouldAddPreFix ? '...' . $sliced : $sliced;
                    $sliced = $shouldAddPostFix ? $sliced . '...' : $sliced;
                }

                $modelRecord->setAttribute('match', $sliced ?? substr($serializedValues, 0, 2 * self::BUFFER . '....'));

                // 2. model -- the model name
                $modelRecord->setAttribute('model', $className);

                // 3. view_link -- url to visit the resource
                $modelRecord->setAttribute('view_link', $this->resolveModelViewLink($modelRecord));
                return $modelRecord;
            });
        })->flatten(1);

        return GlobalSearchResource::collection($results);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return string
     */
    private function resolveModelViewLink(Model $model): string
    {
        // to return a url like: '/{model}->name/{model}->id
        // eg, for posts: /posts/1
        $mapping = [
            Comment::class => '/comments/view/{id}'
        ];
        // get the fully qualified class name of model
        $modelClass = get_class($model);
        // check if class has a $mapping entry, if yes, use that url pattern.
        if (Arr::has($mapping, $modelClass)) {
            return URL::to('{id}', $model->id, $mapping[$modelClass]);
        }
        // otherwise, use the default convention

        // we need to convert the class name to kebab case

        // and return the url

        $modelName = Str::plural(Arr::last(explode('\\', $modelClass)));

        $modelName = Str::kebab(Str::camel($modelName));

        return URL::to('/' . $modelName . '/', $model->id);
    }

    /**
     * @return string
     */
    private function modelNamespacePrefix(): string
    {
        return app()->getNamespace() . 'Models\\';
    }
}
