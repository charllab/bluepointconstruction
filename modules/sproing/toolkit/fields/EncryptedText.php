<?php
/**
 * Sproing module for Craft CMS 3.x
 *
 * fgdsfgdfg
 *
 * @link      https://pluginfactory.io/
 * @copyright Copyright (c) 2018 Sean
 */

namespace modules\sproing\toolkit\fields;

use modules\sproing\toolkit;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\StringHelper;

/**
 * EncryptedField Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for modules to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Sean
 * @package   SproingModule
 * @since     1.0.0
 */
class EncryptedText extends Field
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return 'Encrypted Plain Text';
    }

    // Properties
    // =========================================================================

    /**
     * @var bool Whether the input should allow line breaks
     */
    public $multiline = false;

    /**
     * @var int The minimum number of rows the input should have, if multi-line
     */
    public $initialRows = 4;

    // Public Methods
    // =========================================================================

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        return StringHelper::decdec($value ? $value : '');
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue(StringHelper::encenc($value ? $value : ''), $element);
    }

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('toolkit/_components/fields/EncryptedText_settings',
            [
                'field' => $this
            ]);
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'toolkit/_components/fields/EncryptedText_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
