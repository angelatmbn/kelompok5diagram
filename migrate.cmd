#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to display menu
show_menu() {
    echo -e "${YELLOW}Laravel Migration Helper${NC}"
    echo "1) Run migrations"
    echo "2) Rollback last migration"
    echo "3) Rollback all migrations"
    echo "4) Refresh migrations (rollback all and migrate again)"
    echo "5) Fresh migrations (drop all tables and migrate)"
    echo "6) Reset migrations (rollback all and migrate)"
    echo "7) Show migration status"
    echo "8) Exit"
    echo
    echo -n "Select an option (1-8): "
}

# Function to run migrations
run_migrations() {
    echo -e "${GREEN}Running migrations...${NC}"
    php artisan migrate
}

# Function to rollback last migration
rollback_last() {
    echo -e "${YELLOW}Rolling back last migration...${NC}"
    php artisan migrate:rollback --step=1
}

# Function to rollback all migrations
rollback_all() {
    echo -e "${YELLOW}Rolling back all migrations...${NC}"
    php artisan migrate:reset
}

# Function to refresh migrations
refresh_migrations() {
    echo -e "${YELLOW}Refreshing migrations...${NC}"
    php artisan migrate:refresh
}

# Function to fresh migrations
fresh_migrations() {
    echo -e "${RED}WARNING: This will drop all tables!${NC}"
    read -p "Are you sure you want to continue? (y/n): " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        echo -e "${GREEN}Running fresh migrations...${NC}"
        php artisan migrate:fresh
    else
        echo -e "${YELLOW}Operation cancelled${NC}"
    fi
}

# Function to reset migrations
reset_migrations() {
    echo -e "${YELLOW}Resetting migrations...${NC}"
    php artisan migrate:reset
    php artisan migrate
}

# Function to show migration status
show_status() {
    echo -e "${GREEN}Showing migration status...${NC}"
    php artisan migrate:status
}

# Main loop
while true; do
    show_menu
    read -r choice

    case $choice in
        1) run_migrations ;;
        2) rollback_last ;;
        3) rollback_all ;;
        4) refresh_migrations ;;
        5) fresh_migrations ;;
        6) reset_migrations ;;
        7) show_status ;;
        8)
            echo -e "${GREEN}Goodbye!${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option. Please try again.${NC}"
            ;;
    esac

    echo
    read -p "Press Enter to continue..."
    clear
done