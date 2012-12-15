<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 12.12.12
 * Time: 2:13
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Minifier\Models;

/**
 * @method static \Magelight\Minifier\Models\Minifier forge()
 */
class Minifier
{
    use \Magelight\Forgery;
    use \Magelight\Cache\Cache;

    protected $_staticPath = 'var/static';

    public function __forge()
    {
        $this->_staticPath = \Magelight::app()->config()->getConfigString('global/minifier/public_dir');
    }

    /**
     * @param string $type
     * @return Minifier\MinifierInterface
     */
    protected function getMinifierByType($type = 'css')
    {
        $class = '\\Magelight\\Minifier\\Models\\Minifier\\' . ucfirst(strtolower($type));
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
        if (!\Magelight::app()->config()->getConfigBool('global/minifier/minify_css')) {
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
        if (!\Magelight::app()->config()->getConfigBool('global/minifier/minify_js')) {
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
        if ($isAlreadyMinified = $this->cache()->get($this->buildCacheKey($path), false)) {
            $ok = true;
            if (\Magelight::app()->config()->getConfigBool('global/minifier/check_readability')) {
                $ok = is_readable($path);
            }
            if ($ok) {
                return [
                    'path'    => $path,
                    'content' => '',
                    'url'     => \Magelight::app()->url($path),
                    'inline'  => false
                ];
            }
        }
        foreach ($staticEntries as $entry) {
            if ($entry['inline']) {
                $content .= $minifier->minify($entry['content']);
            } else {
                $buffer = @file_get_contents($entry['path']);
                if ($buffer === false) {
                    throw new \Magelight\Exception("File for minifier cannot be read");
                }
                if (\Magelight::app()->config()->getConfigBool('global/minifier/compress_' . $type)) {
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
        file_put_contents($path, $content);
        $this->cache()->set(
            $this->buildCacheKey($path),
            1,
            \Magelight::app()->config()->getConfigInt('global/minifier/cache_ttl_' . $type)
        );
        return [
            'path'    => $path,
            'content' => '',
            'url'     => \Magelight::app()->url($path),
            'inline'  => false
        ];
    }
}