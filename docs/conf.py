from datetime import datetime

# import specific project config
import os, sys
sys.path.append(os.curdir)
from conf_project import *

author = 'Anton Smirnov'
copyright = '{}'.format(datetime.now().year)
language = 'en'

html_title = project
html_theme = 'sphinx_book_theme'
templates_path = ["_templates"]
html_sidebars = {
    "**": [
        "navbar-logo.html",
        "icon-links.html",
        "rtd-version.html",
        "search-button-field.html",
        "sbt-sidebar-nav.html",
    ]
}
html_theme_options = {
    'use_edit_page_button': True,
    'icon_links': [
        {
            "name": "GitLab",
            "url": "https://gitlab.com/sandfox/" + repo,
            "icon": "fa-brands fa-square-gitlab",
            "type": "fontawesome",
        },
        {
            "name": "GitHub",
            "url": "https://github.com/arokettu/" + repo,
            "icon": "fa-brands fa-square-github",
            "type": "fontawesome",
        },
        {
            "name": "BitBucket",
            "url": "https://bitbucket.org/sandfox/" + repo,
            "icon": "fa-brands fa-bitbucket",
            "type": "fontawesome",
        },
        {
            "name": "Gitea",
            "url": "https://sandfox.org/sandfox/" + repo,
            "icon": "fa-solid fa-mug-hot",
            "type": "fontawesome",
        },
   ]
}
if packagist:
    html_theme_options['icon_links'].append({
        "name": "Packagist",
        "url": "https://packagist.org/packages/" + packagist,
        "icon": "https://img.shields.io/packagist/dm/" + packagist + "?style=flat-square",
        "type": "url",
    })
html_context = {
    'current_version': os.environ.get("READTHEDOCS_VERSION_NAME"),
    'gitlab_user': "sandfox",
    'gitlab_repo': repo,
    'gitlab_version': "master",
    'doc_path': "docs",
}

exclude_patterns = ['venv/*']
