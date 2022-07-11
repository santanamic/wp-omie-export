package main

import (
    "os"
	"fmt"
    "flag"
	"time"
	"strings"
	"strconv"
	"path/filepath"
	"encoding/json"
	"encoding/base64"

    "github.com/xuri/excelize/v2"
)

type Data []struct {
	Seller struct {
		ID             int         `json:"id"`
		Registered     string      `json:"registered"`
		IsNew          bool        `json:"is_new"`
		LegalName      string      `json:"legal_name"`
		BankCode       string      `json:"bank_code"`
		Agencia        string      `json:"agencia"`
		AgenciaDv      string      `json:"agencia_dv"`
		Conta          string      `json:"conta"`
		ContaDv        string      `json:"conta_dv"`
		Type           string      `json:"type"`
		DocumentType   string      `json:"document_type"`
		DocumentNumber string      `json:"document_number"`
		DateCreated    time.Time   `json:"date_created"`
		StoreName      string      `json:"store_name"`
		Email          string      `json:"email"`
		Phone          string      `json:"phone"`
		Address        struct {
			Street1 string `json:"street_1"`
			Street2 string `json:"street_2"`
			City    string `json:"city"`
			Zip     string `json:"zip"`
			Country string `json:"country"`
			State   string `json:"state"`
		} `json:"address"`
	} `json:"seller"`
	Transactions []struct {
		OrderNumber   string    `json:"order_number"`
		Status        string    `json:"status"`
		DateCreated   time.Time `json:"date_created"`
		PaymentMethod string    `json:"payment_method"`
		PaidAmount    int       `json:"paid_amount"`
		Installments  int       `json:"installments"`
		Cost          int       `json:"cost"`
		SplitAmount   int       `json:"split_amount"`
	} `json:"transactions"`
	Split struct {
		SplitTotalAmount   float64    `json:"split_total_amount"`
		SplitOsDescription string `json:"split_os_description"`
	} `json:"data"`
}

func main() {
	deleteTempFiles()
	
	var jsonBase64 string
	
	flag.StringVar(&jsonBase64, "json", "", "Json string")
	
	flag.Parse()

	jsonStr,_ := base64Decode(jsonBase64)

	fmt.Printf(jsonStr)
	
	if jsonStr != "" {
		var data Data
		err := json.Unmarshal([]byte(jsonStr), &data)
		if err != nil {
			fmt.Println(err)
		}
		
		if SpreadsheetOs(data) && SpreadsheetCl(data) {
			fmt.Println("Success")
		}
	}
}

func SpreadsheetCl(data Data) bool {
    f, err := excelize.OpenFile("../assets/xlsx/Omie_Clientes_Fornecedores.xlsx")
    if err != nil {
         fmt.Println(err)
		 return false
    }
    defer func() {
        // Close the spreadsheet.
        if err := f.Close(); err != nil {
            fmt.Println(err)
        }
    }()

	clBaseRow := 6

	for _, i := range data {
		
		if i.Seller.IsNew {
			row := strconv.Itoa(clBaseRow)
			
			f.SetCellValue("Omie_Clientes_Fornecedores", "B" + row, i.Seller.ID)
			f.SetCellValue("Omie_Clientes_Fornecedores", "C" + row, i.Seller.LegalName)
			f.SetCellValue("Omie_Clientes_Fornecedores", "D" + row, i.Seller.DocumentNumber)
			f.SetCellValue("Omie_Clientes_Fornecedores", "E" + row, i.Seller.StoreName)
			f.SetCellValue("Omie_Clientes_Fornecedores", "F" + row, "Sim")
			f.SetCellValue("Omie_Clientes_Fornecedores", "K" + row, i.Seller.Address.Street1)
			f.SetCellValue("Omie_Clientes_Fornecedores", "L" + row, i.Seller.Address.Street2)
			f.SetCellValue("Omie_Clientes_Fornecedores", "O" + row, i.Seller.Address.State)
			f.SetCellValue("Omie_Clientes_Fornecedores", "P" + row, i.Seller.Address.City)
			f.SetCellValue("Omie_Clientes_Fornecedores", "T" + row, i.Seller.Phone)
			f.SetCellValue("Omie_Clientes_Fornecedores", "Y" + row, i.Seller.Email)
			f.SetCellValue("Omie_Clientes_Fornecedores", "AA" + row, i.Seller.BankCode)
			f.SetCellValue("Omie_Clientes_Fornecedores", "AB" + row, i.Seller.Agencia + i.Seller.AgenciaDv) 
			f.SetCellValue("Omie_Clientes_Fornecedores", "AC" + row, i.Seller.Conta + i.Seller.ContaDv)
			
			clBaseRow++
		}
	}
	
	if err := f.SaveAs("../temp/Clientes_Fornecedores.xlsx"); err != nil {
        fmt.Println(err)
		return false
    }
	
	return true
}

func SpreadsheetOs(data Data) bool {
	fmt.Printf("%+v\n", data)

    f, err := excelize.OpenFile("../assets/xlsx/Omie_Ordens_Servico.xlsx")
    if err != nil {
        return false
    }
    defer func() {
        // Close the spreadsheet.
        if err := f.Close(); err != nil {
            fmt.Println(err)
        }
    }()

	osBaseRow := 6

	for _, i := range data {
		row := strconv.Itoa(osBaseRow)
		date := time.Now().AddDate(0, 0, 10).Format("02-01-2006")
			
		f.SetCellValue("Omie_Ordens_Servico", "B" + row, i.Transactions[0].OrderNumber)
		f.SetCellValue("Omie_Ordens_Servico", "C" + row, i.Seller.DocumentNumber)
		f.SetCellValue("Omie_Ordens_Servico", "D" + row, date)
		f.SetCellValue("Omie_Ordens_Servico", "E" + row, "1 Parcela")
		f.SetCellValue("Omie_Ordens_Servico", "H" + row, "Clientes - Serviços Prestados")
		f.SetCellValue("Omie_Ordens_Servico", "I" + row, "Pagar-me")
		f.SetCellValue("Omie_Ordens_Servico", "AA" + row, "02 - Tributado fora do município")
		f.SetCellValue("Omie_Ordens_Servico", "AB" + row, "7490104")
		f.SetCellValue("Omie_Ordens_Servico", "AC" + row, "10.02")
		f.SetCellValue("Omie_Ordens_Servico", "AE" + row, 1)
		f.SetCellValue("Omie_Ordens_Servico", "AF" + row, strings.Replace(fmt.Sprintf("%f", i.Split.SplitTotalAmount/float64(100)), ".", ",", -1))
		f.SetCellValue("Omie_Ordens_Servico", "AI" + row, i.Split.SplitOsDescription)
			
		osBaseRow++
	}
	
	if err := f.SaveAs("../temp/Ordens_Servico.xlsx"); err != nil {
        return false
    }
	
	return true
}

func base64Decode(str string) (string, bool) {
    data, err := base64.StdEncoding.DecodeString(str)
    if err != nil {
        return "", true
    }
    return string(data), false
}

func deleteTempFiles() error {
    files, err := filepath.Glob(filepath.Join("../temp/", "*"))
    if err != nil {
        return err
    }
    for _, file := range files {
        err = os.RemoveAll(file)
        if err != nil {
            return err
        }
    }
    return nil
}