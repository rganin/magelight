<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Core\Models;

/**
 * @method static \Magelight\Core\Models\Minifier forge()
 */
class Minifier
{
    use \Magelight\Traits\TForgery;
    use \Magelight\Traits\TCache;

    protected $_staticPath = 'var/static';

    public function __forge()
    {
        $this->_staticPath = \Magelight\Config::getInstance()->getConfigString('global/minifier/public_dir');
    }

    /**
     * @param string $type
     * @return Minifier\IMinifierInterface
     */
    protected function getMinifierByType($type = 'css')
    {
        $class = '\\Magelight\\Core\\Models\\Minifier\\' . ucfirst(strtolower($type));
        return new $class;
    }

    protected function splitCssByMedia($entries = [])
    {
        $mediaCss = [];
        foreach ($entries as $entry) {
            $mediaCss[$entry['media']][] = $entry;
        }
        return $mediaCss;
    }

    public function getMinifiedCss($css)
    {
        if (!\Magelight\Config::getInstance()->getConfigBool('global/minifier/minify_css')) {
            return $css;
        }
        $css = $this->splitCssByMedia($css);
        $result = [];
        foreach ($css as $media => $styles) {
            $result[] = $this->minifyDocumentStatic('css', $styles) + ['media' => $media];
        }
        return $result;
    }

    public function getMinifiedJs($js)
    {
        if (!\Magelight\Config::getInstance()->getConfigBool('global/minifier/minify_js')) {
            return $js;
        }
        return [$this->minifyDocumentStatic('js', $js)];
    }

    /**
     * Fix urls in CSS file
     *
     * @param string $css
     * @param string $entryPath
     * @return mixed
     */
    protected function fixCssUrls($css, $entryPath)
    {
        $entryPath = preg_replace('/[\\\]+/', '/', dirname($entryPath));
        $staticOffset = preg_replace('/([^\\\\\/]+)/i', '..', $this->_staticPath);
        $staticOffset = preg_replace('/[\\\]+/', '/', $staticOffset);
        $css = preg_replace(
            "/url\s*\(\s*[\"']?([^\"']+)[\"']?\s*\)/i",
            'url("' . $staticOffset . '/'. $entryPath . "/\\1" .'")',
            $css
        );
        return $css;
    }

    protected function getEntriesStaticPath($entries, $type)
    {
        $path = '';
        foreach ($entries as $entry) {
            $path .= $entry['path'];
        }
        return $this->_staticPath . '/' . md5($path) . '.' . $type;
    }

    /**
     * Minify document static entry
     *
     * @param string $type
     * @param array $staticEntries
     * @return array
     * @throws \Magelight\Exception
     */
    protected function minifyDocumentStatic($type = 'css', $staticEntries = [])
    {
        $minifier = $this->getMinifierByType($type);
        $content = '';
        $path = $this->getEntriesStaticPath($staticEntries, $type);
        $dir = dirname($path);
        if (!is_writable($dir)) {
            trigger_error(__("Static cache directory %s is not writable or does not exist!", [$dir]));
            return $staticEntries;
        }
        if ($isAlreadyMinified = $this->cache()->get($this->buildCacheKey($path), false)) {
            $ok = true;
            if (\Magelight\Config::getInstance()->getConfigBool('global/minifier/check_readability')) {
                $ok = is_readable($path);
            }
            if ($ok) {
                return [
                    'path'    => $path,
                    'content' => '',
                    'url'     => \Magelight\Helpers\UrlHelper::getInstance()->getUrl($path),
                    'inline'  => false
                ];
            }
        }
        foreach ($staticEntries as $entry) {
            if ($entry['inline']) {
                $content .= $minifier->minify($entry['content']);
            } else {
                $buffer = @file_get_contents(\Magelight::app()->getRealPathInModules($entry['path']));
                if ($buffer === false) {
                    trigger_error(__("File %s for minifier cannot be read", [$entry['path']]), E_USER_WARNING);
                }
                if (\Magelight\Config::getInstance()->getConfigBool('global/minifier/compress_' . $type)) {
                    $buffer = $minifier->minify($buffer);
                }

                switch ($type) {
                    case 'css':
                        $content .= $this->fixCssUrls($buffer, $entry['path']);
                        break;
                    case 'js':
                        $content .= $buffer;
                        break;
                    default:
                        break;
                }

                unset($buffer);
            }
        }
        if (file_put_contents($path, $content)) {
            $this->cache()->set(
                $this->buildCacheKey($path),
                1,
                \Magelight\Config::getInstance()->getConfigInt('global/minifier/cache_ttl_' . $type)
            );
            return [
                'path'    => $path,
                'content' => '',
                'url'     => \Magelight\Helpers\UrlHelper::getInstance()->getUrl($path),
                'inline'  => false
            ];
        } else {
            return $staticEntries;
        }
    }
}