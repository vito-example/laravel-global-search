<?php
/**
 *  database/factories/CommentFactory.php
 *
 * Date-Time: 02.06.21
 * Time: 14:14
 * @author Vito Makhatadze <vitomaxatadze@gmail.com>
 */

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return $this->create(User::class)->id;
            },
            'post_id' => function () {
                return $this->create(Post::class)->id;
            },
            'body' => $this->faker->realText(150),
        ];
    }
}
