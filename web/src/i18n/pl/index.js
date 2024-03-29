export default {
  validation: {
    required: "Pole jest wymagane",
    email: "To nie jest poprawny adres e-mail",
    minLength: "Wymagane jest minimum {number} znaków",
    maxLength: "Dozwolone jest maximum {number} znaków",
    sameAs: "Wartości muszą być identyczne",
  },
  login: {
    loginButton: "Zaloguj",
    emailAddress: "Adres e-mail",
    password: "Hasło",
    forgotPassword: "Nie pamiętam hasła",
  },
  forgotPassword: {
    emailAddress: "Adres e-mail",
    forgotPassword: "Resetuj hasło",
    returnToLogin: "Powrót do logowania",
    sent: "Na podany adres email został wysłany link do resetowania hasła",
  },
  resetPassword: {
    newPassword1: "Wpisz nowe hasło",
    newPassword2: "Potwórz nowe hasło",
    setNewPassword: "Zapisz",
    done: "Nowe hasło zostało ustawione, możesz go teraz użyć do logowania",
    passwordsDiffer: "Hasła się różnią",
  },
  header: {
    home: "Strona główna",
    stats: "Statystyki",
    administration: "Administracja",
    settings: "Ustawienia",
    logout: "Wyloguj",
    vehicles: "Pojazdy",
    users: "Użytkownicy",
    roles: "Role",
  },
  fuelingsTable: {
    amount: "Ilość",
    mileage: "Przebieg",
    mileageFrom: "Przebieg od",
    mileageTo: "Przebieg do",
    full: "Do pełna",
    fuelConsumption: "Spalanie",
    date: "Data",
    dateFrom: "Data od",
    dateTo: "Data do",
    price: "Cena całkowita",
    pricePerLiter: "Cena za litr",
    empty: "Ten pojazd nie ma jeszcze tankowań",
    avgFuelConsumption: "Średnie spalanie",
    fuel: {
      DIESEL: "Diesel",
      PETROL: "Benzyna",
      LPG: "LPG",
      CNG: "CNG",
      HYDROGEN: "Wodór",
      ELECTRICITY: "Energia elektryczna",
      OTHER: "Inne paliwo",
    },
    add: "Dodaj tankowanie",
    edit: "Edytuj",
    delete: "Usuń",
    noVehicles: "Nie masz jeszcze żadnych pojazdów",
    addingDialogTitle: "Dodawanie tankowania",
    editingDialogTitle: "Edycja tankowania",
    save: "Zapisz",
    cancel: "Anuluj",
    details: "Szczegóły",
    newRoute: "Nowa trasa",
    datePlaceholder: "RRRR-MM-DD",
    deletingFueling: {
      title: "Usuwanie tankowania",
      content: "Czy na pewno chcesz usunąć to tankowanie?",
    },
    filter: "Filtruj",
    filterTitle: "Filtrowanie tankowań",
    apply: "Zastosuj",
    reset: "Wyczyść",
    routeEnd: "Koniec trasy",
  },
  routesTable: {
    routeDetails: "Szczegóły trasy",
    distance: "Dystans",
    avgFuelConsumption: "Średnie spalanie",
    kilometerCost: "Koszt kilometra",
    delete: "Usuń",
    empty: "Ten pojazd nie ma jeszcze tras",
    totalCost: "Koszt całkowity",
    totalAmount: "Ilość paliwa",
    fuelings: "Lista tankowań",
    close: "Zamknij",
  },
  vehicleDetails: {
    settings: "Ustawienia",
    return: "Powrót",
    currentMonth: "Obecny miesiąc",
    prevMonth: "Poprzedni miesiąc",
    ever: "Od początku",
    avgFuelConsumption: "Średnie spalanie",
    distance: "Przejechany dystans",
    totalFuelAmount: "Łącznie zatankowano",
    totalCost: "Koszt wszystkich tankowań",
    kilometerCost: "Koszt 1 km",
  },
  confirmationDialog: {
    confirm: "Tak",
    cancel: "Nie",
  },
};
