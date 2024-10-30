import json
import os

# Function to remove all spaces from the title
def clean_title(title):
    # Remove all spaces
    cleaned_title = title.replace(" ", "")
    return cleaned_title

# Main function to process the JSON data
def process_json(input_file, output_file):
    with open(input_file, 'r') as file:
        data = json.load(file)

    cleaned_data = []
    
    for entry in data:
        title = entry['title']
        page_numbers = entry['page_numbers']
        
        # Clean the title by removing all spaces
        cleaned_title = clean_title(title)

        cleaned_data.append({
            'title': cleaned_title,
            'page_numbers': page_numbers
        })

    with open(output_file, 'w') as file:
        json.dump(cleaned_data, file, indent=4)

# Base path for input files and output directory
base_input_path = '/Users/ahmetaydogan/Downloads/Coin_Search_Engine 2/RCNA Text Source Files/Coin-Text/Index2014/'
output_directory = '/Users/ahmetaydogan/Downloads/Coin_Search_Engine 2/cleaned_files/'

# Ensure the output directory exists
os.makedirs(output_directory, exist_ok=True)

# List of months to loop through
months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

# Loop through each month and process the corresponding file
for month in months:
    input_file_name = f"{month}_2014.json"
    input_file_path = os.path.join(base_input_path, input_file_name)
    
    # Create an output file name based on the month
    output_file_name = f"cleaned_{month}_2014.json"
    output_file_path = os.path.join(output_directory, output_file_name)
    
    # Check if the input file exists before processing
    if os.path.exists(input_file_path):
        print(f"Processing {input_file_name} and saving to {output_file_name}...")
        process_json(input_file_path, output_file_path)
    else:
        print(f"{input_file_name} does not exist.")
