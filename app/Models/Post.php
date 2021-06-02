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
 */
class Post extends Model
{
    use HasFactory;
}
