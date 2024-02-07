from list_class import *
from csv_writer import *
import concurrent.futures # for pool of threads
import os.path # for checking if file exists


out_dir = "ScrapedCSVs/"
max_workers = 4

'''
Letterboxd List scraper - main program
'''

def main():

    target_lists = list()
    if os.path.isfile("target_lists.txt"):
        print('====================================================')
        print('Welcome to the Letterboxd List scraper!')
        print('Scraping the lists specified in target_lists.txt,') 
        print('(letterboxd_url,filename), filename is optional!)') 
        print('Example url: https://letterboxd.com/.../list/short-films/).')
        print('The program currently only supports lists and watchlists.')
        print('====================================================\n')
        with open(f"target_lists.txt",'r') as f:
            reader = csv.reader(f)
            for row in reader:
                if len(row) >1:
                    target_lists.append((row[0],row[1]))
                else:
                    target_lists.append((row[0],None))
    else:
        print('====================================================')
        print('Welcome to the Letterboxd List scraper!')
        print('Provided with an URL, this program outputs a CSV file') 
        print('of movie title, release data and Letterboxd link.') 
        print('Example url: https://letterboxd.com/.../list/short-films/).')
        print('The program currently only supports lists and watchlists.')
        print('Enter q or quit to exit the program.')
        print('====================================================\n')

        list_url=input('Enter the URL of the list you wish to scrape:')
        target_lists.append(((list_url),None))

        # exit option
        if list_url == 'q' or list_url == 'quit':
            exit()

    pool = concurrent.futures.ThreadPoolExecutor(max_workers=max_workers)
    for target in target_lists:
        pool.submit(collect_lists,target[0],target[1]) 
    pool.shutdown(wait=True)





def collect_lists(list_url,passed_name=None):
    
    # Checking if URL is of a watchlist, of a list, or a user's review page
    while True:

        # if a watchlist proceed this way
        if list_url.split('/')[-2] == 'watchlist':
            try:
                list_name = list_url.split('/')[-2]
                username = list_url.split('/')[-3]
                current_list = List(list_name, list_url)
                break

            except:
                print(list_url, ' is not a valid list URL, please try again.')
                continue
        
        # if a list proceed this way
        elif list_url.split('/')[-3] == 'list':
            try:
                list_name = list_url.split('/')[-2]
                list_url = list_url + '/detail/'            # Adding detail to URL access the personal rating later
                current_list = List(list_name, list_url)
                break

            except:
                print(list_url, ' is not a valid list URL, please try again.')
                continue

        # if a user's review page proceed this way
        elif list_url.split('/')[-3] == 'films' and list_url.split('/')[-2] == 'reviews':
            try:
                list_name = list_url.split('/')[-4] + 's_reviews'
                print('trying to scrape reivews from ', list_name)
                current_list = List(list_name, list_url, 'user reviews')
                break

            except:
                print(list_url, ' is not a valid user review page URL, please try again.')
                continue
        # if this is the user's film page proceed this way. This one has proven the most glitchy so far, occasionally throwing errors, but usually executes and always gives correct data when it executes    
        elif list_url.split('/')[-2] == 'films':
            try:
                list_name = list_url.split('/')[-3] + 's_films'
                print('trying to scrape reivews from ', list_name)
                current_list = List(list_name, list_url, 'user reviews')
                break

            except:
                print(list_url, ' is not a valid user review page URL, please try again.')
                continue
        else: #if the url is not a valid list, check if it is a user's account page, or their films page, then move to that user's review page
            
            print('suspecting ', list_url ,' is an account page. Making it ', list_url, 'films/reviews/ and attempting to parse')
            list_url += 'films/reviews/'
                
            #checking if we have successfully made our url into a review page
            if list_url.split('/')[-3] == 'films' and list_url.split('/')[-2] == 'reviews':
                try:
                    list_name = list_url.split('/')[-4] + 's_reviews'
                    print('trying to scrape reivews from ', list_name)
                    current_list = List(list_name, list_url, 'user reviews')
                    break

                except:
                    print(list_url, ' is not a valid user review page URL, please try again.')
                    continue
    
    # writing to a CSV file
    try:
        if passed_name != None:
            csv_path = out_dir + passed_name
        else:
            csv_path = out_dir + username + '_' + list_name
        print(f'Writing to {csv_path}.csv')
        list_to_csv(current_list.films, csv_path)
          
    except:
        if passed_name != None:
            csv_path = out_dir + passed_name
        else:
            csv_path = out_dir + list_name
        print(f'Writing to {csv_path}.csv')
        list_to_csv(current_list.films, csv_path)
    
    print('All Done! Good job cal')

if __name__ == "__main__":
    main()