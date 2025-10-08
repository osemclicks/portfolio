import os

def list_numbered_images(folder_path):
    # Allowed image extensions
    image_extensions = (".jpg", ".jpeg", ".png", ".gif", ".bmp", ".webp","heic")
    
    # Get all files in the folder
    files = os.listdir(folder_path)
    
    # Keep only images that are purely numeric (like 1.jpg, 2.png)
    numbered_images = []
    for f in files:
        name, ext = os.path.splitext(f)
        if ext.lower() in image_extensions and name.isdigit():
            numbered_images.append(f)
    
    # Sort numerically instead of alphabetically
    numbered_images.sort(key=lambda x: int(os.path.splitext(x)[0]))
    
    # Print neatly
    print("Numbered image files:")
    print("-" * 40)
    for img in numbered_images:
        print("\"",end="")
        print(img, end="")
        print("\"",end=",")
    print("-" * 40)
    print(f"Total numbered images: {len(numbered_images)}")

# Example usage:
folder_name = input("Enter the folder name :")
list_numbered_images(folder_name)
