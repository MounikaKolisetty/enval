import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import countryData from '../../assets/json/CountryCodes.json';
import stateData from '../../assets/json/CountryStates.json';
import { PaymentService } from '../services/payment.service';
import { UserService } from '../services/user.service';
import { HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute } from '@angular/router';
declare var Razorpay: any;
@Component({
  selector: 'app-secure-payment',
  standalone: true,
  imports: [ FormsModule,
             CommonModule,
             HttpClientModule,
             ReactiveFormsModule
   ],
  providers: [UserService, PaymentService],
  templateUrl: './secure-payment.component.html',
  styleUrl: './secure-payment.component.css'
})
export class SecurePaymentComponent{
  countries: any[] = [];
  states: any[] = []; 
  selectedCountryCode: string = ''; 
  selectedCountryDialCode: string = '';
  selectedState: string = '';
  courseTitle: string = '';
  paymentForm: FormGroup;
  formSubmitted: boolean = false;

  constructor(private paymentService: PaymentService, private userService: UserService, private route: ActivatedRoute, private fb: FormBuilder) {
    this.paymentForm = this.fb.group({ 
      userName: ['', Validators.required], 
      userContact: ['', [Validators.required, Validators.pattern(/^\d{10}$/)]], 
      userEmail: ['', [Validators.required, Validators.email]], state: ['', Validators.required], 
      selectedCountryCode: ['', Validators.required],
      userDesignation: ['', Validators.required],
      userOrganization: ['', Validators.required],
      userLocation: ['', Validators.required] 
    });
  } 
  
  ngOnInit() { 
    this.loadCountryCodes(); 
    this.route.params.subscribe(params => { 
      this.courseTitle = params['courseTitle']; });
  } 
  loadCountryCodes() { 
      this.countries = countryData; 
      if (this.countries.length > 0) { 
        this.selectedCountryCode = this.countries[0].code; // Set default country code 
        this.selectedCountryDialCode = this.countries[0].dial_code;
        this.paymentForm.patchValue({ selectedCountryCode: this.selectedCountryCode });
          this.loadStates(this.countries[0].code); // Set default states 
      }
  } 

  loadStates(countryCode: string) { 
    const selectedCountry = stateData.find(country => country.code2 === countryCode); 
    this.states = selectedCountry ? selectedCountry.states : []; 
  }
  
  onCountryChange(event: any) { 
    const selectedCountry = this.countries.find(country => country.code === event.target.value);
    this.selectedCountryDialCode = selectedCountry.dial_code;
    this.loadStates(selectedCountry.code);
    (document.getElementById('phone') as HTMLInputElement).placeholder = this.selectedCountryCode + " - Phone Number"; 
  }

  createOrder() { 
    if (this.paymentForm.valid) {
    const formValue = this.paymentForm.value;
    const amount = 20000; // Amount in INR 
    this.userService.createOrder(amount * 100).subscribe( 
      (response:any) => { 
        console.log(response)
        const orderId = response.orderId; 
        this.paymentService.setOrderDetails(amount, formValue.userName, formValue.userEmail, formValue.userContact, orderId, this.courseTitle); 
        this.paymentService.openRazorpay(); 
      }, 
      (error: HttpErrorResponse) => { 
        console.error('Error creating order:', error); 
      } 
    ); 
  }
  else { // Handle invalid form 
    this.formSubmitted = true;
  }
} 

  
}
