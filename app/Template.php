<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'template';
    protected $primaryKey = 'template_id';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the product record associated with the template.
     */
    public function product()
    {
        return $this->hasOne('App\Product', 'template_id');
    }

    /**
     * Parse HTML in template content
     *
     * @param array $data
     * @return mixed
     */
    public function parse(array $data)
    {
        $parsed = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            list($shortCode, $index) = $matches;

            if (isset($data[$index])) {
                return $data[$index];
            } else {
                throw new \Exception("Shortcode {$shortCode} not found in template id {$this->id}", 1);
            }
        }, $this->content);

        return $parsed;
    }
}
