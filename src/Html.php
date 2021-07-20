<?php


namespace antonyz89\mdl;

use yii\helpers\Html as HtmlBase;
use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Html extends HtmlBase
{
    public const BUTTON_CLASS = 'mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect';
    public const BUTTON_COLORS = [
        'red' => 'button--colored-red',
        'teal' => 'button--colored-teal',
        'orange' => 'button--colored-orange',
        'light' => 'button--colored-light',
        'purple' => 'button--colored-purple',
        'green' => 'button--colored-green',
        'white' => 'button--colored-white',
    ];

    public const BUTTON_ICON_CLASS = self::BUTTON_CLASS . ' mdl-button--icon';

    /**
     * @inheritDoc
     *
     * @param string $content
     * @param array|string $options
     * @return string
     */
    public static function button($content = 'Button', $options = [])
    {
        if (is_string($options)) {
            $options = ['class' => $options];
        } if (!isset($options['class'])) {
            $options['class'] = '';
        }

        $options['class'] .= ' ' . self::BUTTON_CLASS;
        return parent::button($content, $options);
    }

    /**
     * @param array|string $content
     * @param array|string $options
     * @return string
     */
    public static function cell($content = '', $options = []): string
    {
        if (empty($options)) {
            $options = 'mdl-cell mdl-cell--12-col';
        } else if (is_array($options)) {
            if (isset($options['class'])) {
                $options['class'] .= ' mdl-cell';
            } else {
                $options['class'] = 'mdl-cell mdl-cell--12-col';
            }
        } else {
            $options .= ' mdl-cell';
        }

        return static::tag('div', $content, $options);
    }

    /**
     * @param array|string $content
     * @param array|string $options
     * @throws Exception
     */
    public static function grid($content = '', $options = [])
    {
        if (is_string($options)) {
            $options .= ' mdl-grid';
        } else {
            $options['class'] = ArrayHelper::getValue($options, 'class') . ' mdl-grid';
        }

        return static::tag('div', $content, $options);
    }

    public static function icon(string $name, $options = []): string
    {
        if (is_array($options)) {
            $options['class'] = ArrayHelper::getValue($options, 'class') . ' material-icons';
        } else {
            $options = [
                'class' => $options . ' material-icons'
            ];
        }

        $tag = ArrayHelper::getValue($options, 'tag', 'i');
        unset($options['tag']);

        return self::tag($tag, $name, $options);
    }

    /**
     * @inheritDoc
     * @param array|string $text
     */
    public static function a($text, $url = null, $options = [])
    {
        if ($url !== null) {
            if (is_array($options)) {
                $options['href'] = Url::to($url);
            } else {
                $options = [
                    'class' => $options,
                    'href' => Url::to($url)
                ];
            }
        }

        return static::tag('a', $text, $options);
    }

    /**
     * @inheritDoc
     *
     * @param array|string $options
     */
    public static function beginTag($name, $options = [])
    {
        if ($name === null || $name === false) {
            return '';
        }

        if (is_string($options)) {
            $options = ['class' => $options];
        }

        return "<$name" . static::renderTagAttributes($options) . '>';
    }

    /**
     * @inheritDoc
     * @param string|array $content the content to be enclosed between the start and end tags. It will not be HTML-encoded.
     * If this is coming from end users, you should consider [[encode()]] it to prevent XSS attacks.
     * @param string|array $options
     */
    public static function tag($name, $content = '', $options = [])
    {
        if (is_array($content)) {
            $_content = '';

            foreach ($content as $key => $value) {
                if (is_string($key)) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $_content .= self::tag('div', $item, $key);
                        }
                    } else {
                        $_content .= self::tag('div', $value, $key);
                    }
                } else {
                    $_content .= $value;
                }
            }

            $content = $_content;
        }

        if (is_string($options)) {
            $options = ['class' => $options];
        }

        return parent::tag($name, $content, $options);
    }

    /**
     * @inheritDoc
     * @param array|string $options
     */
    public static function textInput($name, $value = null, $options = [])
    {
        if (is_string($options)) {
            $options = ['class' => $options];
        }

        return static::input('text', $name, $value, $options);
    }
}
