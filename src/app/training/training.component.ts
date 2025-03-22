import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { MatIconModule } from '@angular/material/icon';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { UserService } from '../services/user.service';
import { RecaptchaModule } from 'ng-recaptcha';
import { HttpClient } from '@angular/common/http';
import { NgxSpinnerModule, NgxSpinnerService } from 'ngx-spinner';

@Component({
  selector: 'app-training',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            CommonModule,
            ScrollButtonComponent,
            MatIconModule,
            RecaptchaModule,
            ReactiveFormsModule,
            NgxSpinnerModule
  ],
  providers: [UserService],
  templateUrl: './training.component.html',
  styleUrl: './training.component.css'
})
export class TrainingComponent {
  @ViewChild('trainings1') trainings1Div!: ElementRef;
  @ViewChild('trainings2') trainings2Div!: ElementRef;
  isContactVisible: boolean = false; 
  userForm!: FormGroup;
  captchaResolved: boolean = false;
  messageSent: boolean = false;
  captchaResponse: string | null = null;
  constructor(
    private fb: FormBuilder, 
    private userService: UserService, 
    private http: HttpClient,
    private spinner: NgxSpinnerService,
  ) { }
  ngOnInit(){
      this.userForm = this.fb.group({
        name: ['', Validators.required],
        designation: ['', Validators.required],
        organization: ['', Validators.required], 
        location: ['', Validators.required], 
        trainees: ['', Validators.required], 
        email: ['', Validators.required], 
        mobile: ['', Validators.required]
      })
    }
    scrollToTrainings1() {
      const element = this.trainings1Div.nativeElement;
      const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
      window.scrollTo({ top: top, behavior: 'smooth' });
    }
    scrollToTrainings2() {
      const element = this.trainings2Div.nativeElement;
      const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
      window.scrollTo({ top: top, behavior: 'smooth' });
    }
  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
  resolved(captchaResponse: string | null) { 
    console.log('Captcha Response:', captchaResponse); 
    this.captchaResponse = captchaResponse;
    this.captchaResolved = !!captchaResponse; // Set to true if captchaResponse is not empty 
  }
  onSubmit(){
    if(this.userForm.valid && this.captchaResolved){
      const captchaToken = this.captchaResponse ?? ''; 
    // if(this.userForm.valid){
      this.userService.sendTrainingForm(this.userForm.value, captchaToken).subscribe(
        response => {
          console.log('Email sent successfully', response); 
          this.messageSent = true;
          this.userForm.reset();
        }, 
        error => { 
          console.error('Error sending email', error); 
        }
      );
    }
  }
  downloadFile() { 
    this.spinner.show();
    const fileUrl = '../../assets/Corporate Brochure.pdf'; 
    // Replace with your file URL 
    this.http.get(fileUrl, { responseType: 'blob' }).subscribe((response: Blob) => { 
      const downloadURL = window.URL.createObjectURL(response); 
      const link = document.createElement('a'); 
      link.href = downloadURL; 
      link.download = 'Corporate Brochure.pdf'; // Replace with the desired file name 
      link.click(); 
      this.spinner.hide();
    }, 
    error => { 
      console.error('Error downloading the file', error); 
      this.spinner.hide();
    }); 
  }
  downloadFlyer() { 
    const fileUrl = '../../assets/Flyer_PVA.pdf'; 
    // Replace with your file URL 
    this.http.get(fileUrl, { responseType: 'blob' }).subscribe((response: Blob) => { 
      const downloadURL = window.URL.createObjectURL(response); 
      const link = document.createElement('a'); 
      link.href = downloadURL; 
      link.download = 'PVA Flyer.pdf'; // Replace with the desired file name 
      link.click(); 
    }, 
    error => { 
      console.error('Error downloading the file', error); 
    }); 
  }
}
