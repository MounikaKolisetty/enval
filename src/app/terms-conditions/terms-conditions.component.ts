import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { FooterComponent } from '../footer/footer.component';

@Component({
  selector: 'app-terms-conditions',
  standalone: true,
  imports: [NavbarComponent,
            ScrollButtonComponent,
            FooterComponent
  ],
  templateUrl: './terms-conditions.component.html',
  styleUrl: './terms-conditions.component.css'
})
export class TermsConditionsComponent {

}
