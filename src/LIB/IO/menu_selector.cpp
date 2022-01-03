#include <iostream>
#include <string>
#include <vector>
#include <sstream>
#include <utility>
#include <curses.h>
#include <stdlib.h>

int wrap(const int value, const int min, const int max);
std::vector<std::string> explode(const std::string input, const char delimiter);
void printOptions(const std::vector<std::string> options, const int selectedIndex = 0);

int main(int argc, char** argv)
{
    using namespace std;

    if (argc == 1)
    {
        cout << "Nothing to do" << endl;
        return -1;
    }

    vector<string> options;

    int selectedIndex = 0;
    string color = "";

    for (int i = 2; i < argc; i++)
    {
        options.push_back(argv[i]);
    }
    int totalOptions = options.size();

    initscr();
    cbreak();
    noecho();

    curs_set(0);

    bool isRunning = true;
    char c;

    do
    {
        string title = argv[1];
        if (title.length() > 0)
        {
            cout << title << endl;
        }
     
        printOptions(options, selectedIndex);
        printf("\033[H");

        c = getch();

        if (c != '\n' && c == '\033')
        {
            getch();
            c = getch();
            switch (c)
            {
                case 'A':
                    --selectedIndex;
                    break;

                case 'B':
                    ++selectedIndex;
                    break;

                default:
                    break;
            }
            refresh();
        }
        selectedIndex = wrap(selectedIndex, 0, totalOptions);
    } while (c != '\n');

    endwin();

    cout << options.at(selectedIndex) << endl;

    return 0;
}

void printOptions(const std::vector<std::string> options, const int selectedIndex)
{
    using namespace std;
    int count = options.size();
    string color = "\033[1;34m";
    string line = "";

    for (int i = 0; i < count; i++)
    {
        color = i == selectedIndex ? "\033[1;34m❯ " : "\033[0m  ";

        line = color + options.at(i) + "\033[0m"; 
        cout << "\r" << line << endl;
    }
}

int wrap(const int value, const int min, const int max)
{
    if (value < min)
    {
        return max - 1;
    }

    if (value >= max)
    {
        return min;
    }

    return value;
}

std::vector<std::string> explode(const std::string &input, const char delimiter)
{
    using namespace std;

    vector<string> result;
    istringstream iss(input);
    string token = "";

    // for (std::string token; getline(iss, token, delimiter); )
    for(int i = 0; i < input.length(); i++)
    {
        if (input.at(i) == delimiter && token.length() > 0)
        {
            result.push_back(std::move(token));
            token = "";
        }
        else
        {
            token += input.at(i);
        }
    }

    return result;
}