<?php

namespace LaravelBoot\Foundation;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\ViewServiceProvider;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Application extends Container
{
	const VERSION = '0.1.0';
}