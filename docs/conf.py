from datetime import datetime

# import specific project config
import os, sys
sys.path.append(os.curdir)
from conf_project import *

author = 'Anton Smirnov'
copyright = '{} {}'.format(datetime.now().year, author)
language = 'en'

html_title = project
html_theme = 'furo'
templates_path = ["_templates"]
html_sidebars = {
    "**": [
        "sidebar/brand.html",
        "rtd-version.html",
        "sidebar/search.html",
        "sidebar/scroll-start.html",
        "sidebar/navigation.html",
        "sidebar/ethical-ads.html",
        "sidebar/scroll-end.html",
        "sidebar/variant-selector.html",
    ]
}
