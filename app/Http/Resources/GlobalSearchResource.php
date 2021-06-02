<?php
/**
 *  app/Http/Resources/GlobalSearchResource.php
 *
 * Date-Time: 02.06.21
 * Time: 14:58
 * @author Vito Makhatadze <vitomaxatadze@gmail.com>
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GlobalSearchResource
 * @package App\Http\Resources
 */
class GlobalSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'match' => $this->match,
            'model' => $this->model,
            'view_link' => $this->view_link
        ];
    }
}
