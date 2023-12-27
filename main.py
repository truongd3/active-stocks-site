import requests
from bs4 import BeautifulSoup
from pymongo import MongoClient
import time

client = MongoClient('mongodb://localhost:27017/') # MongoDB setup
db = client['truong_data'] # Database
collection = db['most_active_stocks'] # Table

def print_MongoDB():
    cursor = collection.find()
    for document in cursor:
        print(document)

def delete_data():
    collection.drop()

def isfloat(num):
    try:
        float(num)
        return True
    except ValueError:
        return False

def get_data():
    delete_data()
    
    url = 'https://finance.yahoo.com/most-active'

    try:
        response = requests.get(url) # Send an HTTP request
        print("Status Code:", response.status_code)
        page = BeautifulSoup(response.text, "html.parser") # Load the HTML content into BeautifulSoup
        rows = page.find_all("tr") # Load content of tag <tr>

        fields = {"idx": [], "Symbol": [], "Name": [], "Price (Intraday)": [], "Change": [], "Volume": []} # Local db as hashmap
        idx = 1
        for row in rows: # <tr>
            fields["idx"].append(idx)
            idx += 1
            for column in row: # <td>
                field = column.get("aria-label") # 5 fields only
                if field in fields.keys():
                    if field == "Volume":
                        fields[field].append(float(column.text[0:-1]))
                    else:
                        fields[field].append(float(column.text) if isfloat(column.text) else column.text)
                    #fields[field].append(column.text)
                    #print(field, column.text)
        print("Done adding to dictionary")

        for i in range(idx):
            symbol = fields["Symbol"][i]
            if collection.count_documents({'Symbol': symbol}) == 0: # Check if the document exists by Symbol
                getidx = fields["idx"][i]
                name = fields["Name"][i]
                price = fields["Price (Intraday)"][i]
                change = fields["Change"][i]
                volume = fields["Volume"][i]
                collection.insert_one({'_id': getidx, 'Symbol': symbol, 'Name': name, 'Price': price, 'Change': change, 'Volume': volume})
        print("Done adding to db")
        
    except:
        print("Error! Retry!")

round = 0
while round <= 5:
    print("Turn", round)
    get_data()
    print_MongoDB()
    time.sleep(180)  # Sleep for 3 minutes
    round += 1