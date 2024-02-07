from list_scraper import *


class List:
    """
    List to store data pertaining to a specific list
    """
    
    def __init__(self, list_name, link, type = 'list'):
        """
        :param list_name: List name for data file (if applicable):
        :param link: The link of the list
        """
        
        self.name = list_name
        self.link = link
        print("\nScraping list data...\n")
        if type == 'list':
            self.films = scrape_list(self.link)
        elif type == 'user reviews':
            self.films = scrape_list(self.link)