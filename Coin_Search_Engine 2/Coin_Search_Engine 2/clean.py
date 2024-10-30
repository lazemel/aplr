import json

def clean_title(title):
    """Removes extra whitespace from a title."""
    return " ".join(title.split())

with open("/Users/ahmetaydogan/Downloads/Coin_Search_Engine 2/RCNA Text Source Files/Coin-Text/Index2014/April_2014.json", "r") as f:
    data = json.load(f)

# Clean up titles and create a new list of cleaned data
cleaned_data = []
for item in data:
    cleaned_title = clean_title(item["title"])
    cleaned_item = {"title": cleaned_title, "page_numbers": item["page_numbers"]}
    cleaned_data.append(cleaned_item)

# Save the cleaned data to a new JSON file
with open("cleaned_json_file.json", "w") as f:
    json.dump(cleaned_data, f, indent=4)