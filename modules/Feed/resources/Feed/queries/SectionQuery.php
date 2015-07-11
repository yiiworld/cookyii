<?php
/**
 * SectionQuery.php
 * @author Revin Roman
 * @link https://rmrevin.ru
 */

namespace resources\Feed\queries;

/**
 * Class SectionQuery
 * @package resources\Feed\queries
 */
class SectionQuery extends \yii\db\ActiveQuery
{

    use
        \components\db\traits\query\ActivatedQueryTrait,
        \components\db\traits\query\DeletedQueryTrait;

    /**
     * @param integer|array $id
     * @return static
     */
    public function byId($id)
    {
        $this->andWhere(['id' => $id]);

        return $this;
    }

    /**
     * @param integer|array $parent_id
     * @return static
     */
    public function byParentId($parent_id)
    {
        $this->andWhere(['parent_id' => $parent_id]);

        return $this;
    }

    /**
     * @param string|array $slug
     * @return static
     */
    public function bySlug($slug)
    {
        $this->andWhere(['slug' => $slug]);

        return $this;
    }

    /**
     * @return static
     */
    public function onlyPublished()
    {
        $this
            ->onlyActivated()
            ->withoutDeleted()
            ->andWhere(['<=', 'published_at', time()])
            ->andWhere(['or', ['>=', 'archived_at', time()], ['archived_at' => null]]);

        return $this;
    }

    /**
     * @param string $query
     * @return static
     */
    public function search($query)
    {
        $words = explode(' ', $query);

        $this->andWhere([
            'or',
            array_merge(['or'], array_map(function ($value) { return ['like', 'id', $value]; }, $words)),
            array_merge(['or'], array_map(function ($value) { return ['like', 'slug', $value]; }, $words)),
            array_merge(['or'], array_map(function ($value) { return ['like', 'title', $value]; }, $words)),
            array_merge(['or'], array_map(function ($value) { return ['like', 'meta', $value]; }, $words)),
        ]);

        return $this;
    }
}