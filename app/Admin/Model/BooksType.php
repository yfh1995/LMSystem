<?php namespace App\Admin\Model;
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/2/17
 * Time: 10:19
 */

use Illuminate\Database\Eloquent\Model;

class BooksType extends Model{

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = 'books_type';

        parent::__construct($attributes);
    }

    /**
     * Build options of select field in form.
     *
     * @param array  $elements
     * @param int    $parentId
     * @param string $prefix
     *
     * @return array
     */
    public static function buildSelectOptions(array $elements = [], $parentId = 0, $prefix = '',$nbsp = 4)
    {
        $prefix = $prefix ?: str_repeat('&nbsp;', $nbsp);

        $options = [];

        if (empty($elements)) {
            $elements = static::orderByRaw('`sort` = 0,`sort`')->get(['id', 'parent_id', 'type_name'])->toArray();
        }

        foreach ($elements as $element) {
            $element['type_name'] = $prefix.'&nbsp;'.$element['type_name'];
            if ($element['parent_id'] == $parentId) {
                $children = static::buildSelectOptions($elements, $element['id'], $prefix.$prefix);

                $options[$element['id']] = $element['type_name'];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

}