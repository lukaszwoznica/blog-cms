<?php

namespace Core;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class View
{

    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $view_file = dirname(__DIR__) . "/App/Views/$view.php";

        if (is_readable($view_file))
            require_once $view_file;
        else
            echo "$view_file not found!";
    }

    public static function renderTemplate($template, $data = []): void
    {
        static $twig = null;

        if ($twig === null){
            $loader = new FilesystemLoader(dirname(__DIR__) . "/App/Views");
            $twig = new Environment($loader);
        }

        try {
            echo $twig->render($template . '.twig', $data);
        } catch (LoaderError $e) {
            echo $e->getMessage();
        } catch (RuntimeError $e) {
            echo $e->getMessage();
        } catch (SyntaxError $e) {
            echo $e->getMessage();
        }
    }
}