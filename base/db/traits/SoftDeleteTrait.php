<?php
/**
 * SoftDeleteTrait.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\db\traits;

/**
 * Trait SoftDeleteTrait
 * @package cookyii\db\traits
 *
 * Трейт переопределяет метод delete в ActiveRecord классе.
 * Теперь, при первом вызове метода delete, запись не будет физически удалена из базы данных,
 * а только лишь помечается удалённой (deleted=1)
 *
 * При повторном вызове метода delete, запись будет удалена полностью из базы
 *
 * @property bool $deleted_at
 */
trait SoftDeleteTrait
{

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return !empty($this->deleted_at);
    }

    /**
     * @return bool
     */
    public function isNotDeleted()
    {
        return !$this->isDeleted();
    }

    /**
     * @param bool $permanently если true, то запись будет безусловно удалена, восстановить (@see restore) её будет нельзя
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws \yii\base\InvalidConfigException
     */
    public function delete($permanently = false)
    {
        if (!$this->hasAttribute('deleted_at') && !$this->hasProperty('deleted_at')) {
            throw new \yii\base\InvalidConfigException(sprintf('`%s` has no attribute named `%s`.', get_class($this), 'deleted_at'));
        }

        if (true === $permanently || $this->isDeleted()) {
            // permanently delete
            $result = parent::delete();
        } elseif (!$this->beforeDelete()) {
            $result = false;
        } else {
            // soft delete
            $this->deleted_at = time();

            $result = $this->update();

            $this->afterDelete();
        }

        return $result;
    }

    /**
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws \yii\base\InvalidConfigException
     */
    public function restore()
    {
        if (!$this->hasAttribute('deleted_at') && !$this->hasProperty('deleted_at')) {
            throw new \yii\base\InvalidConfigException(sprintf('`%s` has no attribute named `%s`.', get_class($this), 'deleted_at'));
        }

        $this->deleted_at = null;

        return $this->update();
    }
}