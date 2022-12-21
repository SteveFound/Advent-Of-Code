#include <stdio.h>
#include <stdlib.h>
#include <string.h>

/*
 * Replace newline characters "\n" with string terminators "\0"
*/
char *chompNewline(char *p) {
    char *q = p;
    for( char *q = p; *q; q++ ){
        if( *q == '\n' ){
            *q = '\0';
            break;
        }
    }
    return p;
}

/**
 * Function to count the groups of numbers in file which is simply counting the number of blank lines.
*/
int countGroups( FILE *fp ) {
    char *buffer = malloc(20);            // Assume no line will be more than 20 characters
    int count = 0;
    while(fgets(buffer, 19, fp) != NULL) {
        if( strlen(chompNewline(buffer)) == 0 ){
            count++;
        }
    }
    fseek(fp, 0, SEEK_SET);                // Rewind the file pointer to the start of the file
    if( strlen(buffer) > 0 ) {             // Add 1 if the last line wasn't blank
        count++;
    }
    free(buffer);
    return count;                          // Return the number of blank lines
}

/**
 * Read each group of numbers, add them together and write the sum of each group to an integer array
*/
void sumGroups( FILE *fp, int *counts, int size) {
    char *buffer = malloc(20);  
    int sum = 0;          
    while( fgets(buffer,19, fp) != NULL ) {
        chompNewline(buffer);
        if( strlen(buffer) > 0 ) {
            sum += atoi(buffer);
        } else {
            *counts++ = sum;
            sum = 0;
        }
    }
    if( strlen(buffer) > 0 ) {             // If last line wasn't blank, do the last total
        *counts = sum;
    }
    free(buffer);
}

/**
 * qsort compare function for sorting in descending order
*/
int compare( const void* a, const void* b)
{
     int intA = *((int*) a);
     int intB = *((int*) b);

     if ( intA == intB ) return 0;
     if ( intA < intB ) return 1;
     return -1;
}

int main() {
    FILE *fp = fopen("day1input.txt", "r");

    // Count how many elves there are
    int elves = countGroups(fp);
    // Create an array to hold total calories for each elf then zero it
    int counts[elves];
    for( int i = 0 ; i < elves; i++ ){
        counts[i] = 0;
    }
    // Count the calories for each elf
    sumGroups(fp, counts, elves);
    fclose(fp);

    // Find the highest total
    int max = 0;
    int idx = 0;
    for(int elf = 0; elf < elves; elf++) {
        if( counts[elf] > max ){
            max = counts[elf];
            idx = elf;
        }
    }
    printf("Part 1: Elf %d is highest with %d\n", idx+1, max);
    
    // Sort the totals into descending order
    qsort( counts, elves, sizeof(int), compare);

    long part2 = counts[0] + counts[1] + counts[2];
    printf( "Part 2: Sum of 3 highest %ld \n", part2 );

    return 0;
}