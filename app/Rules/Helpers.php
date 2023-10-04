<?php

namespace App\Rules;

class Helpers
{
    public static string $filledString255 = 'filled|string|max:255';
    public static string $requiredString255 = 'required|string|max:255';
    public static string $requiredString = 'required|string';
    public static string $filledString = 'filled|string';

    public static string $requiredDate = 'required|date_format:Y-m-d';
    public static string $filledDate = 'filled|date_format:Y-m-d';

    public static string $filledArray = 'filled|array';
    public static string $requiredArray = 'required|array';

    public static string $requiredFile20mb = 'required|file|max:20480';
    public static string $requiredFileImage2mb = 'required|file|mimes:webp,jpg,png|max:2048';
    public static string $sometimesFileImage2mb = 'sometimes|file|mimes:webp,jpg,png|max:2048';
}
