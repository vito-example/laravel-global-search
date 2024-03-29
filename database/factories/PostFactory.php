<?php
/**
 *  database/factories/PostFactory.php
 *
 * Date-Time: 02.06.21
 * Time: 14:10
 * @author Vito Makhatadze <vitomaxatadze@gmail.com>
 */
namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' =>User::all()->random()->id,
            'title' => $this->faker->realText(40),
            'body' => $this->faker->realText(150),
        ];
    }
}
