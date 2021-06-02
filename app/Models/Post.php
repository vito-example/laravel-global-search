<?php
/**
 *  app/Models/Post.php
 *
 * Date-Time: 02.06.21
 * Time: 13:18
 * @author Vito Makhatadze <vitomaxatadze@gmail.com>
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 * @package App\Models
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $body
 * @property string $created_at
 * @property string $updated_at
 */
class Post extends Model
{
    use HasFactory;
}
