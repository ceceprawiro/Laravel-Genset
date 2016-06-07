<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Illuminate\View\View;
use Illuminate\View\Factory;

class Genset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'genset {entity : The entity name to create, example: item.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generator set';

    /**
     * The entity to create.
     *
     * @var string
     */
    protected $entity;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->entity = strtolower($this->argument('entity'));

        $this->createMigration();
        $this->createSeeder();
        $this->createModel();
        $this->createController();
        $this->createView();
    }

    private function createMigration()
    {
        $this->call('make:migration', [
            'name'     => $this->entity,
            '--create' => $this->entity,
            '--table'  => $this->entity,
        ]);
    }

    private function createSeeder()
    {
        $this->call('make:seeder', [
            'name' => ucfirst($this->entity).'TableSeeder',
        ]);
    }

    private function createModel()
    {
        $this->call('make:model', [
            'name' => ucfirst($this->entity),
        ]);
    }

    private function createController()
    {
        $this->call('make:controller', [
            'name'       => ucfirst($this->entity).'Controller',
            '--resource' => true,
        ]);
    }

    private function createView()
    {
        $baseViewPath   = 'resources/views/';
        $errorPath      = $baseViewPath.'error/';
        $layoutPath     = $baseViewPath.'layout/';
        $viewPath       = $baseViewPath.$this->entity.'/';

        $fs = new Filesystem();

        /* create view directory */
        try {
            $fs->mkdir($viewPath);
        } catch(IOExceptionInterface $e) {
            $this->error("An error occurred while creating your directory at ".$e->getPath());
        }

        /* create layout directory */
        try {
            $fs->mkdir($layoutPath);
        } catch(IOExceptionInterface $e) {
            $this->error("An error occurred while creating your directory at ".$e->getPath());
        }

        /* create views */
        $templates = ['index', 'create', 'show', 'edit'];

        foreach ($templates as $template) {
            $content = file_get_contents(__DIR__.'/templates/'.$template.'.blade.php');

            try {
                $fs->dumpFile($viewPath.$template.'.blade.php', $content);
            } catch(IOExceptionInterface $e) {
                $this->error("An error occurred while creating view (".$e->getPath() . ')');
            }
        }

        $this->info('Views created successfully.');
    }
}
