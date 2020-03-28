<?php

namespace Core;

use App\Auth;
use App\Flash;
use App\Models\Category;
use App\Models\Settings;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View
{
    /**
     * Render a view file
     *
     * @param string $view
     * @param array $data
     * @throws Exception
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $view_file = dirname(__DIR__) . "/App/Views/$view.php";

        if (is_readable($view_file))
            require_once $view_file;
        else
            throw new Exception("$view_file not found");
    }

    /**
     * Get the content of a view template using Twig
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public static function getTemplate(string $template, array $data = []): string
    {
        static $twig = null;

        if ($twig === null){
            $loader = new FilesystemLoader(dirname(__DIR__) . "/App/Views");
            $twig = new Environment($loader);

            $rememberPageFunction = new TwigFunction('rememberRequestedPage', function () {
                Auth::rememberRequestedPage();
            });
            $twig->addFunction($rememberPageFunction);

            // Twig globals
            $twig->addGlobal('current_user', Auth::getUser());
            $twig->addGlobal('flash_messages', Flash::getMessages());
            $twig->addGlobal('categories', Category::getTree());
            $twig->addGlobal('request_uri', $_SERVER['REQUEST_URI']);
            $twig->addGlobal('site_settings', new Settings());
        }

        try {
            return $twig->render($template . '.twig', $data);
        } catch (LoaderError $e) {
            Error::exceptionHandler($e, true);
        } catch (RuntimeError $e) {
            Error::exceptionHandler($e, true);
        } catch (SyntaxError $e) {
            Error::exceptionHandler($e, true);
        }

        return '';
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template
     * @param array $data
     */
    public static function renderTemplate(string $template, array $data = []): void
    {
        echo static::getTemplate($template, $data);
    }
}