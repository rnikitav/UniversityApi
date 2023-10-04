<?php

namespace App\Http\Requests\Accelerator;

use App\Models\File;
use App\Models\Tags\ImageCollection;
use App\Rules\Helpers;
use Illuminate\Foundation\Http\FormRequest;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Support\Arr;
use App\Repositories\File\FileRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @property array $control_points
 * @property array $files
 * @property array $tags
 * @property integer $image_main
 */
class Create extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => Helpers::$requiredString255,
            'description' => 'nullable|string',
            'published_at' => 'nullable|date_format:Y-m-d',
            'date_end_accepting' => Helpers::$requiredDate,
            'date_end' => Helpers::$requiredDate,
            'image_main' => 'required|integer',
            'control_points' => Helpers::$requiredArray,
            'control_points.*.name' => Helpers::$requiredString255,
            'control_points.*.date_completion' => Helpers::$requiredDate,
            'control_points.*.max_score' => 'required|integer|between:1,65535',
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb,
            'tags' => Helpers::$requiredArray,
            'tags.*.id' => 'required|integer|exists:tags,id',
        ];
    }

    protected function passedValidation()
    {
        /** @var FileRepository $fileRepository */
        $fileRepository = $this->container->make(FileRepository::class);
        /** @var File $file */
        $file = $fileRepository->byId($this->image_main);
        if (is_null($file) || !($file->owner instanceof ImageCollection)) {
            throw new UnprocessableEntityHttpException(__('exception.invalid_file', ['field' => 'image_main']));
        }
        /** @var ImageCollection $owner */
        $owner = $file->owner;

        $diffTags = $owner->tags->pluck('id')->intersect(Arr::pluck($this->tags, 'id'));
        if ($diffTags->count() == 0) {
            throw new UnprocessableEntityHttpException(__('exception.invalid_file', ['field' => 'image_main']));
        }
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['user_id'] = $this->user()?->id;
        $data['image_main_id'] = Arr::pull($data, 'image_main');
        $data['tags'] = Arr::pluck($this->tags, 'id');
        return $data;
    }
}

