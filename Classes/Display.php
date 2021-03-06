<?php
/**
 * This file is part of SmartWork.
 *
 * SmartWork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SmartWork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SmartWork.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   SmartWork
 * @author    Marian Pollzien <map@wafriv.de>
 * @copyright (c) 2015, Marian Pollzien
 * @license   https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
namespace SmartWork;

/**
 * Handles the displaying of pages.
 *
 * @package SmartWork
 * @author  Marian Pollzien <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Display
{
    /**
     * The global config object.
     *
     * @var \SmartWork\GlobalConfig
     */
    protected $globalConfig;

    /**
     * A list of pages which are not accessible.
     *
     * @var array
     */
    protected $unallowedPages = array();

    /**
     * A list of pages which are accessible without login.
     *
     * @var array
     */
    protected $pagesWithoutLogin = array(
        'Register',
        'Login',
        'Imprint',
        'LostPassword',
    );

    /**
     * A list of unallowed pages which will be added to the existing list.
     *
     * @param array $unallowedPages
     */
    function __construct(array $unallowedPages = array())
    {
        $this->globalConfig = GlobalConfig::getInstance();
        $this->unallowedPages = array_merge($unallowedPages, $this->unallowedPages);
        $globalUnallowedPages = $this->globalConfig->getConfig(
            array(
                'Display' => 'pagesWithoutLogin',
            )
        );

        if (!is_array($globalUnallowedPages['Display']))
        {
            $globalUnallowedPages['Display'] = array();
        }

        $this->pagesWithoutLogin = array_merge(
            $globalUnallowedPages['Display'],
            $this->pagesWithoutLogin
        );
    }

    /**
     * Display the given page.
     *
     * @param string $pageName
     *
     * @return void
     */
    public function showPage(string $pageName)
    {
        $pageName = $this->checkPage($pageName);

        // Create the page itself
        $class = '\\Page\\'.$pageName;

        if ($this->globalConfig->getConfig('useModules')
            && !class_exists($class)
        )
        {
            foreach ($this->globalConfig->getConfig('modules') as $module)
            {
                if (class_exists('\\' . $module . '\\Page\\' . $pageName))
                {
                    $class = '\\' . $module . '\\Page\\' . $pageName;
                    break;
                }

                if (class_exists('\\SmartWork\\' . $module . '\\Page\\' . $pageName))
                {
                    $class = '\\SmartWork\\' . $module . '\\Page\\' . $pageName;
                    break;
                }
            }
        }

        /* @var $page \SmartWork\Page */
        $page = new $class();

        if ($page->getTemplate() && !$page->isAjax())
        {
            // Create the page header
            if (class_exists('\\Page\\Header'))
            {
                $header = new \Page\Header($page->getTemplate());
            }
            else
            {
                $header = new \SmartWork\Page\Header($page->getTemplate());
            }

            $header->process();
        }

        $page->process();
        $page->render();
    }

    /**
     * Check if the page is in the list of unallowed pages. If so, return 'Index'.
     *
     * @param string $pageName
     *
     * @return string
     */
    protected function checkPage(string $pageName): string
    {
        $checkPageHooks = $this->globalConfig->getHook(
            array(
                'Display' => 'checkPage',
            )
        );

        if ($checkPageHooks)
        {
            foreach ($checkPageHooks as $hook)
            {
                $result = $hook($pageName);

                if ($result)
                {
                    $pageName = $result;
                    break;
                }
            }
        }

        if (in_array($pageName, $this->unallowedPages))
        {
            return 'Index';
        }

        $useModules = $this->globalConfig->getConfig('useModules');
        $modules = $this->globalConfig->getConfig('modules');

        if ($useModules && in_array('UserSystem', $modules) && !$_SESSION['userId']
            && !in_array($pageName, $this->pagesWithoutLogin)
        )
        {
            $pageName = 'Login';
        }

        return $pageName;
    }

    /**
     * Add multiple pages to the list of unallowed pages.
     *
     * @param array $pageNames
     *
     * @return void
     */
    public function addUnallowedPages(array $pageNames)
    {
        $this->unallowedPages += $pageNames;
    }

    /**
     * Add a single page to the list of unallowed pages.
     *
     * @param string $pageName
     *
     * @return void
     */
    public function addUnallowedPage(string $pageName)
    {
        $this->addUnallowedPages(array($pageName));
    }

    /**
     * Remove multiple pages from the list of unallowed pages.
     *
     * @param array $pageNames
     *
     * @return void
     */
    public function removeUnallowedPages(array $pageNames)
    {
        foreach ($pageNames as $pageName)
        {
            $index = array_search($pageName, $this->unallowedPages);
            unset($this->unallowedPages[$index]);
        }
    }

    /**
     * Remove a single page from the list of unallowed pages.
     *
     * @param string $pageName
     *
     * @return void
     */
    public function removeUnallowedPage(string $pageName)
    {
        $this->removeUnallowedPages(array($pageName));
    }

    /**
     * Clear the list of unallowed pages.
     *
     * @return void
     */
    public function clearUnallowedPages()
    {
        $this->unallowedPages = array();
    }
}
