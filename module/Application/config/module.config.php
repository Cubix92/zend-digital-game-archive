<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\I18n\Translator;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/api',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'api-notes' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/notes[/:id]',
                            'defaults' => [
                                'controller' => Controller\Api\NoteController::class
                            ],
                        ]
                    ],
                    'api-tags' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/tags[/:id]',
                            'defaults' => [
                                'controller' => Controller\Api\TagController::class
                            ],
                        ]
                    ],
                    'api-categories' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/categories[/:id]',
                            'defaults' => [
                                'controller' => Controller\Api\CategoryController::class
                            ],
                        ]
                    ],
                ]
            ],
            'category' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/category[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\CategoryController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'note' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/note[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\NoteController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
            'tag' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/tag[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\TagController::class,
                        'action'     => 'index'
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            Model\CategoryRepository::class => Factory\CategoryRepositoryFactory::class,
            Model\CategoryCommand::class => Factory\CategoryCommandFactory::class,
            Model\CategoryHydrator::class => InvokableFactory::class,
            Model\NoteRepository::class => Factory\NoteRepositoryFactory::class,
            Model\NoteCommand::class => Factory\NoteCommandFactory::class,
            Model\NoteHydrator::class => InvokableFactory::class,
            Model\TagRepository::class => Factory\TagRepositoryFactory::class,
            Model\TagCommand::class => Factory\TagCommandFactory::class,
            Model\TagService::class => Factory\TagServiceFactory::class,
            Model\TagHydrator::class => InvokableFactory::class,
            Listener\TagListener::class => Factory\TagListenerFactory::class,
            Service\Slugger::class => InvokableFactory::class,
        ],
        'delegators' => [
            Translator::class => [
                Delegator\TranslatorDelegator::class
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\Api\NoteController::class => Factory\Api\NoteControllerFactory::class,
            Controller\Api\TagController::class => Factory\Api\TagControllerFactory::class,
            Controller\Api\CategoryController::class => Factory\Api\CategoryControllerFactory::class,
            Controller\CategoryController::class => Factory\CategoryControllerFactory::class,
            Controller\NoteController::class => Factory\NoteControllerFactory::class,
            Controller\TagController::class => Factory\TagControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\CategoryForm::class => Factory\CategoryFormFactory::class,
            Form\NoteForm::class => Factory\NoteFormFactory::class,
            Form\TagFieldset::class => Factory\TagFieldsetFactory::class,
        ]
    ],
    'input_filters' => [
        'factories' => [
            Form\CategoryInputFilter::class => InvokableFactory::class,
            Form\NoteInputFilter::class => Factory\NoteInputFilterFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Użytkownicy',
                'route' => 'user',
                'pages' => [
                    [
                        'label'  => 'Dodawanie użytkownika',
                        'route'  => 'user',
                        'action' => 'add',
                    ],
                    [
                        'label'  => 'Edycja użytkownika',
                        'route'  => 'user',
                        'action' => 'edit',
                    ]
                ]
            ],
            [
                'label' => 'Kategorie',
                'route' => 'category',
                'pages' => [
                    [
                        'label'  => 'Dodawanie kategorii',
                        'route'  => 'category',
                        'action' => 'add',
                    ],
                    [
                        'label'  => 'Edycja kategorii',
                        'route'  => 'category',
                        'action' => 'edit',
                    ]
                ],
            ],
            [
                'label' => 'Notatki',
                'route' => 'note',
                'pages' => [
                    [
                        'label'  => 'Dodawanie notatki',
                        'route'  => 'note',
                        'action' => 'add',
                    ],
                    [
                        'label'  => 'Edycja notatki',
                        'route'  => 'note',
                        'action' => 'edit',
                    ]
                ],
            ],
            [
                'label' => 'Tagi',
                'route' => 'tag',
                'pages' => [
                    [
                        'label'  => 'Podgląd tagu',
                        'route'  => 'tag',
                        'action' => 'show',
                    ]
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/user',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'user/user/user' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/user'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ]
];
