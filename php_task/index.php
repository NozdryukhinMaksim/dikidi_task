<?php
// Константы для корневой директории и разрешенных расширений файлов
const BASE_DIR = __DIR__ . '/';
const ALLOWED_EXTENSIONS = [ 'jpg', 'jpeg', 'png', 'gif', 'svg' ];

/**
 * Получает текущий путь на основе GET-параметра и проверяет его безопасность.
 * Если путь выходит за пределы BASE_DIR, доступ запрещается.
 */
function get_current_path( string $baseDir ): string {
	$relativePath = $_GET['path'] ?? '';
	$currentPath  = realpath( $baseDir . '/' . $relativePath );

	// Проверка, что путь находится внутри BASE_DIR и является директорией
	if ( ! $currentPath || ! str_starts_with( $currentPath, realpath( $baseDir ) ) || ! is_dir( $currentPath ) ) {
		die( 'Доступ запрещен' );
	}

	return $currentPath;
}

/**
 * Сканирует директорию и возвращает массив с папками и файлами.
 * Фильтрует файлы по разрешенным расширениям.
 */
function get_directory_items( string $path ): array {
	$items   = scandir( $path ) ?: [];
	$folders = [];
	$files   = [];

	foreach ( $items as $item ) {
		// Пропускаем служебные элементы "." и ".."
		if ( $item === '.' || $item === '..' ) {
			continue;
		}

		$fullPath = $path . DIRECTORY_SEPARATOR . $item;

		// Разделяем папки и файлы
		if ( is_dir( $fullPath ) ) {
			$folders[] = $item;
		} elseif ( is_file( $fullPath ) ) {
			// Фильтруем файлы по расширениям
			$extension = strtolower( pathinfo( $fullPath, PATHINFO_EXTENSION ) );
			if ( in_array( $extension, ALLOWED_EXTENSIONS ) ) {
				$files[] = $item;
			}
		}
	}

	// Сортируем папки и файлы
	sort( $folders );
	sort( $files );

	return [ 'folders' => $folders, 'files' => $files ];
}

// Получаем текущий путь и список элементов в директории
$currentPath    = get_current_path( BASE_DIR );
$directoryItems = get_directory_items( $currentPath );

// Разбиваем путь на части для хлебных крошек
$pathParts   = explode( DIRECTORY_SEPARATOR, trim( str_replace( realpath( BASE_DIR ), '', $currentPath ), DIRECTORY_SEPARATOR ) );
$currentLink = '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Файловый менеджер</title>
    <style>
        .breadcrumbs { margin-bottom: 30px; }
        .breadcrumbs a { text-decoration: none; color: #007bff; font-weight: bold; }
        .breadcrumbs a:hover { text-decoration: underline; }
        .container { max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; }
        .item { padding: 15px 10px; border-bottom: 1px solid #ddd; display: flex; align-items: center; }
        .item:hover { background: #f3f3f3; }
        .thumb { max-width: 50px; max-height: 50px; margin-right: 10px; }
        .folder-thumb { max-width: 15px; max-height: 15px; margin-right: 5px; }
        .file-list a { text-decoration: none; color: #333; }
        .file-list a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h1>Файловый менеджер</h1>
    <div class="breadcrumbs">
        <a href="/">Главная</a>
		<?php foreach ( $pathParts as $part ): ?>
			<?php if ( ! empty( $part ) ): ?>
				<?php $currentLink .= '/' . $part; ?>
                / <a href="?path=<?= urlencode( $currentLink ) ?>"><?= htmlspecialchars( $part ) ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
    </div>

    <div class="file-list">
		<?php if ( $currentPath !== realpath( BASE_DIR ) ): ?>
            <a href="?path=<?= urlencode( dirname( str_replace( realpath( BASE_DIR ), '', $currentPath ) ) ) ?>">
                <div class="item">
                    ← Назад
                </div>
            </a>
		<?php endif; ?>

		<?php if ( empty( $directoryItems['folders'] ) && empty( $directoryItems['files'] ) ): ?>
            <div class="item">
                Папка пуста
            </div>
		<?php endif; ?>

		<?php foreach ( $directoryItems['folders'] as $folder ): ?>
            <a href="?path=<?= urlencode( str_replace( realpath( BASE_DIR ), '', $currentPath . '/' . $folder ) ) ?>">
                <div class="item">
                    <img class="folder-thumb" src="./assets/folder.svg" alt="folder icon">
					<?= htmlspecialchars( $folder ) ?>
                </div>
            </a>
		<?php endforeach; ?>

		<?php foreach ( $directoryItems['files'] as $file ): ?>
            <a href="<?= htmlspecialchars( str_replace( realpath( __DIR__ ), '', $currentPath . '/' . $file ) ) ?>"
               target="_blank">
                <div class="item">
                    <img class="thumb"
                         src="<?= htmlspecialchars( str_replace( realpath( __DIR__ ), '', $currentPath . '/' . $file ) ) ?>"
                         alt="thumbnail">
					<?= htmlspecialchars( $file ) ?>
                </div>
            </a>
		<?php endforeach; ?>
    </div>
</div>
</body>
</html>