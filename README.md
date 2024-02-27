# Youtube Playlist Downloader

This project allows you to download songs from **public** YouTube playlists in MP3 format and save them to a local folder. It uses the `pytube` library for downloading and `moviepy` for format conversion.

## Requirements

Before you begin, make sure you have Python installed on your system. Additionally, you will need to install the following libraries:

- pytube
- movie

You can install these dependencies with the following command:

```tap
pip install pytube moviepy
```

Finally, navigate to the playlist you want to download, and copy the id of the playlist you want to download (what appears to the right of list=)




## Usage

To use this script, follow these steps:

1. Clone this repository locally.

2. Open a terminal or command line.

3. Navigate to the directory where the script is located.

4. Run the script with the YouTube playlist ID as an argument:

```tap
python3 download_playlist.py playlist_id --output output_directory
```

Replace `[playlist ID]` with the actual ID of the YouTube playlist you want to download.

## Example
```tap
python3 download_playlist.py playlist_id --output output_directory
```