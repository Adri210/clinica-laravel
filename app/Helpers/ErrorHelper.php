<?php

namespace App\Helpers;

class ErrorHelper
{
    public static function showErrorModal($message)
    {
        // Não limpa o buffer, apenas adiciona o modal
        echo "
        <div style='
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        '>
            <div style='
                background-color: white;
                padding: 20px;
                border-radius: 5px;
                width: 80%;
                max-width: 400px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            '>
                <h2 style='color: #dc2626; margin-bottom: 15px;'>Erro</h2>
                <p style='color: #4b5563; margin-bottom: 20px;'>{$message}</p>
                <a href='javascript:history.back()' style='
                    background-color: #dc2626;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    display: inline-block;
                '>Voltar</a>
            </div>
        </div>";
        exit;
    }
}