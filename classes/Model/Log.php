<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log extends Model
{
    /**
     * Старые методы (оставим, если где-то ещё используются)
     */
    public function getList()
    {
        $dir = Kohana::$config->load('artonitcity_config')->dir_log;
        return $this->getDirectoryTree($dir, 'log');
    }

    public function getListFramework()
    {
        $dir = Kohana::$config->load('artonitcity_config')->dir_log_framework;
        return $this->getDirectoryTree($dir, 'log');
    }
	
	
	public function getListArtonitServices()
    {
        $dir = Kohana::$config->load('artonitcity_config')->ArtonitServices;
        return $this->getDirectoryTree($dir, 'log');
    }
	
	

    public function getListCompare()
    {
        $dir = Kohana::$config->load('artonitcity_config')->dir_compare;
        return $this->getDirectoryTree($dir, 'csv');
    }

    /**
     * Получить содержимое ОДНОГО уровня директории.
     *
     * @param string $rootDir  Корневая директория (внутрь которой нельзя выходить)
     * @param string $subPath  Относительный путь внутри корня ('' = сам корень)
     * @param array  $exts     Список расширений для показа файлов (пусто = все файлы)
     * @return array [
     *     'path'    => 'относительный/путь',   // текущий относительный путь
     *     'parent'  => 'относительный/путь',   // родитель (NULL если корень)
     *     'dirs'    => [ ['name'=>'2026','rel'=>'2026'], ... ],
     *     'files'   => [ ['name'=>'04.php','rel'=>'2026/10/04.php','size'=>614,'mtime'=>...], ... ],
     * ]
     */
    public function listDirectory($rootDir, $subPath = '', $exts = array())
    {
        $rootDir = rtrim($rootDir, '\\/');

        // Нормализуем относительный путь (защита от ..)
        $subPath = $this->normalizeRelPath($subPath);

        $currentDir = $rootDir . ($subPath !== '' ? DIRECTORY_SEPARATOR . $subPath : '');

        if (!is_dir($currentDir)) {
            // Если путь битый — отдаём корень
            $subPath = '';
            $currentDir = $rootDir;
        }

        $result = array(
            'path'   => $subPath,
            'parent' => $this->parentRelPath($subPath),
            'dirs'   => array(),
            'files'  => array(),
        );

        if (!is_dir($currentDir)) {
            return $result;
        }

        $items = @scandir($currentDir);
        if ($items === FALSE) {
            return $result;
        }

        foreach ($items as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $full = $currentDir . DIRECTORY_SEPARATOR . $name;
            $rel  = ($subPath !== '' ? $subPath . '/' : '') . $name;

            if (is_dir($full)) {
                $result['dirs'][] = array(
                    'name' => $name,
                    'rel'  => $rel,
                );
            } elseif (is_file($full)) {
                // Фильтр по расширению (если задан)
                if (!empty($exts)) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (!in_array($ext, array_map('strtolower', $exts), TRUE)) {
                        continue;
                    }
                }

                $result['files'][] = array(
                    'name'  => $name,
                    'rel'   => $rel,
                    'size'  => filesize($full),
                    'mtime' => filemtime($full),
                );
            }
        }

        // Сортировка: папки по имени, файлы — по mtime (свежие сверху)
        usort($result['dirs'], function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        usort($result['files'], function ($a, $b) {
            return $b['mtime'] - $a['mtime'];
        });

        return $result;
    }

    /**
     * Нормализация относительного пути: убираем .., ведущие слеши.
     */
    public function normalizeRelPath($rel)
    {
        $rel = str_replace('\\', '/', (string) $rel);
        $rel = trim($rel, '/');

        if ($rel === '') {
            return '';
        }

        $parts = array();
        foreach (explode('/', $rel) as $p) {
            if ($p === '' || $p === '.') {
                continue;
            }
            if ($p === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $p;
        }

        return implode('/', $parts);
    }

    /**
     * Родительский относительный путь.
     */
    public function parentRelPath($rel)
    {
        $rel = $this->normalizeRelPath($rel);
        if ($rel === '') {
            return NULL; // корень — выше некуда
        }
        $pos = strrpos($rel, '/');
        if ($pos === FALSE) {
            return '';
        }
        return substr($rel, 0, $pos);
    }

    /**
     * Скачать указанный файл
     */
    public function send_file($file)
    {
        if (!is_file($file)) {
            throw new HTTP_Exception_404('File not found: ' . basename($file));
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $basename = basename($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $basename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit;
    }

    /**
     * Рекурсивный обход (оставлен для совместимости).
     */
    public function getDirectoryTree($outerDir, $ext)
    {
        $result = array();
        $outerDir = rtrim($outerDir, '\\/');

        if (!is_dir($outerDir)) {
            return $result;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $outerDir,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $baseLen = strlen($outerDir) + 1;

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if (strtolower($file->getExtension()) !== strtolower($ext)) {
                continue;
            }
            $fullPath = $file->getPathname();
            $relPath  = str_replace('\\', '/', substr($fullPath, $baseLen));
            $result[$relPath] = $fullPath;
        }

        krsort($result);
        return $result;
    }
}